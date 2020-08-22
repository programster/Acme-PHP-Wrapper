<?php

/*
 * A driver that can be used to to interface with the DigitalOcean service.
 */

class DigitalOceanDriver implements AcmeDnsDriverInterface
{
    private $m_client;


    /**
     * Create the driver.
     * @param string $apiKey - the key for write access to AWS Route 53
     */
    public function __construct(string $apiKey)
    {
        $digitalOcean = new \DigitalOceanV2\Client();
        $digitalOcean->authenticate($apiKey);
        $this->m_client = $digitalOcean->domainRecord();
        $this->m_client->perPage(100);
    }


    /**
     * Add a TXT record using Route53
     * @param string $name - the TXT record FQDN. E.g. "test.mydomin.org"
     * @param string $value - the value for the TXT record.
     * @return void - throw exception if anything goes wrong.
     */
    public function addTxtRecord(string $name, string $value)
    {
        $this->m_client->create(
            $this->getDomainFromFQDN($name),
            "TXT",
            $this->getSubdomainForFQDN($name),
            $value,
            null,
            null,
            null,
            null,
            null,
            60
        );
    }


    private function getDomainFromFQDN($FQDN)
    {
        $parts = explode(".", $FQDN);
        $numParts = count($parts);
        $domain = $parts[$numParts - 2] . '.' . $parts[$numParts - 1];
        return $domain;
    }


    private function getSubdomainForFQDN($FQDN)
    {
        $parts = explode(".", $FQDN);
        $numParts = count($parts);

        // remove the last two elements which are the domain.
        array_pop($parts);
        array_pop($parts);

        $subdomain = implode(".", $parts);
        return $subdomain;
    }
}
