<?php

namespace SonarSoftware\CustomerPortalFramework\Controllers;

use Exception;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;
use SonarSoftware\CustomerPortalFramework\Models\Ticket;

class AccountTicketController
{
    private $httpHelper;
    /**
     * AccountAuthenticationController constructor.
     */
    public function __construct()
    {
        $this->httpHelper = new HttpHelper();
    }

    /*
     * GET functions
     */
    /**
     * Get all the public tickets for an account (see https://sonar.software/apidoc/index.html#api-Tickets-GetEntityTickets)
     * @param $accountID
     * @param int $page
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function getTickets($accountID, $page = 1)
    {
        $result = $this->httpHelper->get("/accounts/" . intval($accountID) . "/tickets", $page);
        $tickets = [];
        foreach ($result as $datum)
        {
            if ($datum->type !== "public")
            {
                continue;
            }
            $ticket = new Ticket([
                'subject' => $datum->subject,
                'ticket_group_id' => $datum->ticket_group_id,
                'user_id' => $datum->user_id,
                'account_id' => $datum->assignee_id,
                'priority' => $datum->priority,
                'open' => $datum->open,
                'inbound_email_account_id' => $datum->inbound_email_account_id,
                'email_address' => $datum->email_address,
                'ticket_id' => $datum->id,
                'last_reply_incoming' => $datum->last_reply_incoming,
            ]);
            array_push($tickets, $ticket);
        }

        return $tickets;
    }

    /**
     * Get all the replies on a ticket (see https://sonar.software/apidoc/index.html#api-Tickets-GetTicketReplies)
     * @param Ticket $ticket
     * @param int $page
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function getReplies(Ticket $ticket, $page = 1)
    {
        return $this->httpHelper->get("/tickets/" . intval($ticket->getTicketID()) . "/ticket_replies", $page);
    }
    
    /*
     * POST functions
     */

    /**
     * Create a new ticket. The first input from the customer will be posted as an incoming reply.
     * @param Ticket $ticket
     * @param $author
     * @param $authorEmail
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function createTicket(Ticket $ticket, $author, $authorEmail)
    {
        if (filter_var($authorEmail, FILTER_VALIDATE_EMAIL) === false)
        {
            throw new InvalidArgumentException($authorEmail . " is not a valid email address.");
        }

        $result = $this->httpHelper->post("/tickets", [
            'subject' => $ticket->getSubject(),
            'type' => 'public',
            'inbound_email_account_id' => $ticket->getInboundEmailAccountID(),
            'ticket_group_id' => $ticket->getTicketGroupID(),
            'user_id' => $ticket->getUserID(),
            'assignee' => 'accounts',
            'assignee_id' => $ticket->getAccountID(),
            'email_address' => $ticket->getEmailAddress(),
            'priority' => $ticket->getPriority(),
            'comment' => "Created via the customer portal framework.",
        ]);

        $this->httpHelper->post("/tickets/" . $result->id . "/ticket_replies", [
            'text' => $ticket->getDescription(),
            'incoming' => true,
            'author' => $author,
            'author_email' => $authorEmail,
        ]);

        return $result;
    }

    /**
     * Post a reply to an existing ticket
     * @param Ticket $ticket
     * @param $replyText
     * @param $author
     * @param $authorEmail
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function postReply(Ticket $ticket, $replyText, $author, $authorEmail)
    {
        if (filter_var($authorEmail, FILTER_VALIDATE_EMAIL) === false)
        {
            throw new InvalidArgumentException($authorEmail . " is not a valid email address.");
        }

        try {
            $this->httpHelper->patch("/tickets/{$ticket->getTicketID()}",[
                'open' => true,
            ]);
        }
        catch (Exception $e)
        {
            //
        }

        return $this->httpHelper->post("/tickets/{$ticket->getTicketID()}/ticket_replies",[
            'text' => $replyText,
            'incoming' => true,
            'author' => $author,
            'author_email' => $authorEmail,
        ]);
    }
}