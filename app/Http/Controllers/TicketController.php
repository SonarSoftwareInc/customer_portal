<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketReplyRequest;
use App\Http\Requests\TicketRequest;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountTicketController;
use SonarSoftware\CustomerPortalFramework\Exceptions\ApiException;
use SonarSoftware\CustomerPortalFramework\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Return ticket listing
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tickets = $this->getTickets();
        return view("pages.tickets.index", compact('tickets'));
    }

    /**
     * Show an individual ticket
     * @param $id
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        //We need to ensure that this ticket belongs to this user
        $tickets = $this->getTickets();
        foreach ($tickets as $ticket) {
            try {
                if ($ticket->getTicketID() == $id) {
                    $accountTicketController = new AccountTicketController();
                    $replies = $accountTicketController->getReplies($ticket, 1);
                    //Clear the cache here, because you may see a ticket with ISP responses but the list may not show it yet
                    $this->clearTicketCache();
                    return view("pages.tickets.show", compact('replies', 'ticket'));
                }
            } catch (ApiException $e) {
                Log::error($e->getMessage());
                $this->clearTicketCache();
                return redirect()->action("TicketController@index")->withErrors(utrans("errors.ticketNotFound"));
            }

        }

        return redirect()->action("TicketController@index")->withErrors(utrans("errors.invalidTicketID"));
    }

    /**
     * Show ticket creation page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $emailAddress = get_user()->email_address;
        if ($emailAddress == null) {
            return redirect()->action("ProfileController@show")->withErrors(utrans("errors.mustSetEmailAddress"));
        }
        return view("pages.tickets.create");
    }

    /**
     * Create a new ticket
     * @param TicketRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(TicketRequest $request)
    {
        try {
            $ticket = new Ticket([
                'account_id' => get_user()->account_id,
                'email_address' => get_user()->email_address,
                'subject' => $request->input('subject'),
                'description' => $request->input('description'),
                'ticket_group_id' => config("customer_portal.ticket_group_id"),
                'priority' => config("customer_portal.ticket_priority"),
                'inbound_email_account_id' => config("customer_portal.inbound_email_account_id"),
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.failedToCreateTicket"))->withInput();
        }

        $accountTicketController = new AccountTicketController();
        try {
            $accountTicketController->createTicket($ticket, get_user()->contact_name, get_user()->email_address);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.failedToCreateTicket"))->withInput();
        }

        $this->clearTicketCache();
        return redirect()->action("TicketController@index")->with('success', utrans("tickets.ticketCreated"));
    }

    /**
     * Post a reply to a ticket
     * @param $ticketID
     * @param TicketReplyRequest $request
     * @return $this
     */
    public function postReply($ticketID, TicketReplyRequest $request)
    {
        $accountTicketController = new AccountTicketController();
        $tickets = $this->getTickets();
        foreach ($tickets as $ticket) {
            if ($ticket->getTicketID() == $ticketID) {
                try {
                    $accountTicketController->postReply($ticket, $request->input('reply'), get_user()->contact_name, get_user()->email_address);
                    return redirect()->back()->with('success', utrans("tickets.replyPosted"));
                } catch (Exception $e) {
                    return redirect()->back()->withErrors(utrans("errors.failedToPostReply"));
                }
            }
        }

        return redirect()->back()->withErrors(utrans("errors.invalidTicketID"));
    }

    /**
     * Clear the ticket cache
     */
    private function clearTicketCache()
    {
        Cache::tags("tickets")->forget(get_user()->account_id);
    }

    /**
     * Get tickets, cache them if currently uncached, otherwise return from cache
     * @return mixed
     */
    private function getTickets()
    {
        if (!Cache::tags("tickets")->has(get_user()->account_id)) {
            $accountTicketController = new AccountTicketController();
            $tickets = $accountTicketController->getTickets(get_user()->account_id);
            Cache::tags("tickets")->put(get_user()->account_id, $tickets, 10);
        }
        return Cache::tags("tickets")->get(get_user()->account_id);
    }
}
