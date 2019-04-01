<?php
require_once("/usr/share/portal/vendor/autoload.php");
$currentVersion = (string)file_get_contents("/usr/share/portal/resources/version");
echo "Checking for new upgrades newer than $currentVersion!\n";

$client = new GuzzleHttp\Client();
$res = $client->get("https://api.github.com/repos/sonarsoftware/customer_portal/tags");
$body = json_decode($res->getBody()->getContents());
$latestVersion = $body[0]->name;

if (version_compare($currentVersion, $latestVersion) === -1)
{
    echo "There is a newer version, $latestVersion.\n";
    exec("/usr/bin/php /usr/share/portal/artisan down");
    exec("/bin/rm -f /tmp/customerPortal.zip");
    exec("/usr/bin/wget -O /tmp/customerPortal.zip https://github.com/SonarSoftware/customer_portal/archive/$latestVersion.zip",$output,$returnVar);
    if ($returnVar !== 0)
    {
        echo "Failed to download customer portal file. Try again later.\n";
        return;
    }
    else
    {
        exec("/usr/bin/unzip -o /tmp/customerPortal.zip -d /tmp");
        if (!file_exists("/tmp/customer_portal-$latestVersion"))
        {
            echo "Failed to unzip the customer portal. Try again later.\n";
            return;
        }
        else
        {
            exec("/bin/cp -R /tmp/customer_portal-$latestVersion/portal/* /usr/share/portal/");
            exec("/bin/chown -R www-data:www-data /usr/share/portal");
        }
    }

    echo "Files copied, performing upgrade steps.\n";

    exec("/bin/rm -f /usr/share/portal/bootstrap/cache/*");

    exec("/usr/bin/php /usr/share/portal/artisan up");
    exec("/usr/bin/php /usr/share/portal/artisan migrate --force");
    exec("/usr/bin/php /usr/share/portal/artisan cache:clear");
    exec("/usr/bin/php /usr/share/portal/artisan view:clear");
    exec("/usr/bin/php /usr/share/portal/artisan route:cache");
    exec("/usr/bin/php /usr/share/portal/artisan config:cache");

    echo "Portal successfully updated.\n";
    return;
}

echo "You are on the latest version.\n";