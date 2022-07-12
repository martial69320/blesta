<?php
//
// WARNING: Do not edit by hand, this file was generated by Crank:
// https://github.com/gocardless/crank
//

namespace GoCardlessPro\Integration;

class CreditorsIntegrationTest extends IntegrationTestBase
{
    public function testResourceModelExists()
    {
        $obj = new \GoCardlessPro\Resources\Creditor(array());
        $this->assertNotNull($obj);
    }
    
    public function testCreditorsCreate()
    {
        $fixture = $this->loadJsonFixture('creditors')->create;
        $this->stub_request($fixture);

        $service = $this->client->creditors();
        $response = call_user_func_array(array($service, 'create'), (array)$fixture->url_params);

        $body = $fixture->body->creditors;
    
        $this->assertInstanceOf('\GoCardlessPro\Resources\Creditor', $response);

        $this->assertEquals($body->address_line1, $response->address_line1);
        $this->assertEquals($body->address_line2, $response->address_line2);
        $this->assertEquals($body->address_line3, $response->address_line3);
        $this->assertEquals($body->can_create_refunds, $response->can_create_refunds);
        $this->assertEquals($body->city, $response->city);
        $this->assertEquals($body->country_code, $response->country_code);
        $this->assertEquals($body->created_at, $response->created_at);
        $this->assertEquals($body->id, $response->id);
        $this->assertEquals($body->links, $response->links);
        $this->assertEquals($body->logo_url, $response->logo_url);
        $this->assertEquals($body->name, $response->name);
        $this->assertEquals($body->postal_code, $response->postal_code);
        $this->assertEquals($body->region, $response->region);
        $this->assertEquals($body->scheme_identifiers, $response->scheme_identifiers);
        $this->assertEquals($body->verification_status, $response->verification_status);
    

        $expectedPathRegex = $this->extract_resource_fixture_path_regex($fixture);
        $dispatchedRequest = $this->history[0]['request'];
        $this->assertRegExp($expectedPathRegex, $dispatchedRequest->getUri()->getPath());
    }

    public function testCreditorsCreateWithIdempotencyConflict()
    {
        $fixture = $this->loadJsonFixture('creditors')->create;

        $idempotencyConflictResponseFixture = $this->loadFixture('idempotent_creation_conflict_invalid_state_error');

        // The POST request responds with a 409 to our original POST, due to an idempotency conflict
        $this->mock->append(new \GuzzleHttp\Psr7\Response(409, [], $idempotencyConflictResponseFixture));

        // The client makes a second request to fetch the resource that was already
        // created using our idempotency key. It responds with the created resource,
        // which looks just like the response for a successful POST request.
        $this->mock->append(new \GuzzleHttp\Psr7\Response(200, [], json_encode($fixture->body)));

        $service = $this->client->creditors();
        $response = call_user_func_array(array($service, 'create'), (array)$fixture->url_params);
        $body = $fixture->body->creditors;

        $this->assertInstanceOf('\GoCardlessPro\Resources\Creditor', $response);

        $this->assertEquals($body->address_line1, $response->address_line1);
        $this->assertEquals($body->address_line2, $response->address_line2);
        $this->assertEquals($body->address_line3, $response->address_line3);
        $this->assertEquals($body->can_create_refunds, $response->can_create_refunds);
        $this->assertEquals($body->city, $response->city);
        $this->assertEquals($body->country_code, $response->country_code);
        $this->assertEquals($body->created_at, $response->created_at);
        $this->assertEquals($body->id, $response->id);
        $this->assertEquals($body->links, $response->links);
        $this->assertEquals($body->logo_url, $response->logo_url);
        $this->assertEquals($body->name, $response->name);
        $this->assertEquals($body->postal_code, $response->postal_code);
        $this->assertEquals($body->region, $response->region);
        $this->assertEquals($body->scheme_identifiers, $response->scheme_identifiers);
        $this->assertEquals($body->verification_status, $response->verification_status);
        

        $expectedPathRegex = $this->extract_resource_fixture_path_regex($fixture);
        $conflictRequest = $this->history[0]['request'];
        $this->assertRegExp($expectedPathRegex, $conflictRequest->getUri()->getPath());
        $getRequest = $this->history[1]['request'];
        $this->assertEquals($getRequest->getUri()->getPath(), '/creditors/ID123');
    }
    
    public function testCreditorsList()
    {
        $fixture = $this->loadJsonFixture('creditors')->list;
        $this->stub_request($fixture);

        $service = $this->client->creditors();
        $response = call_user_func_array(array($service, 'list'), (array)$fixture->url_params);

        $body = $fixture->body->creditors;
    
        $records = $response->records;
        $this->assertInstanceOf('\GoCardlessPro\Core\ListResponse', $response);
        $this->assertInstanceOf('\GoCardlessPro\Resources\Creditor', $records[0]);

        $this->assertEquals($fixture->body->meta->cursors->before, $response->before);
        $this->assertEquals($fixture->body->meta->cursors->after, $response->after);
    

    
        foreach (range(0, count($body) - 1) as $num) {
            $record = $records[$num];
            $this->assertEquals($body[$num]->address_line1, $record->address_line1);
            $this->assertEquals($body[$num]->address_line2, $record->address_line2);
            $this->assertEquals($body[$num]->address_line3, $record->address_line3);
            $this->assertEquals($body[$num]->can_create_refunds, $record->can_create_refunds);
            $this->assertEquals($body[$num]->city, $record->city);
            $this->assertEquals($body[$num]->country_code, $record->country_code);
            $this->assertEquals($body[$num]->created_at, $record->created_at);
            $this->assertEquals($body[$num]->id, $record->id);
            $this->assertEquals($body[$num]->links, $record->links);
            $this->assertEquals($body[$num]->logo_url, $record->logo_url);
            $this->assertEquals($body[$num]->name, $record->name);
            $this->assertEquals($body[$num]->postal_code, $record->postal_code);
            $this->assertEquals($body[$num]->region, $record->region);
            $this->assertEquals($body[$num]->scheme_identifiers, $record->scheme_identifiers);
            $this->assertEquals($body[$num]->verification_status, $record->verification_status);
            
        }

        $expectedPathRegex = $this->extract_resource_fixture_path_regex($fixture);
        $dispatchedRequest = $this->history[0]['request'];
        $this->assertRegExp($expectedPathRegex, $dispatchedRequest->getUri()->getPath());
    }

    
    public function testCreditorsGet()
    {
        $fixture = $this->loadJsonFixture('creditors')->get;
        $this->stub_request($fixture);

        $service = $this->client->creditors();
        $response = call_user_func_array(array($service, 'get'), (array)$fixture->url_params);

        $body = $fixture->body->creditors;
    
        $this->assertInstanceOf('\GoCardlessPro\Resources\Creditor', $response);

        $this->assertEquals($body->address_line1, $response->address_line1);
        $this->assertEquals($body->address_line2, $response->address_line2);
        $this->assertEquals($body->address_line3, $response->address_line3);
        $this->assertEquals($body->can_create_refunds, $response->can_create_refunds);
        $this->assertEquals($body->city, $response->city);
        $this->assertEquals($body->country_code, $response->country_code);
        $this->assertEquals($body->created_at, $response->created_at);
        $this->assertEquals($body->id, $response->id);
        $this->assertEquals($body->links, $response->links);
        $this->assertEquals($body->logo_url, $response->logo_url);
        $this->assertEquals($body->name, $response->name);
        $this->assertEquals($body->postal_code, $response->postal_code);
        $this->assertEquals($body->region, $response->region);
        $this->assertEquals($body->scheme_identifiers, $response->scheme_identifiers);
        $this->assertEquals($body->verification_status, $response->verification_status);
    

        $expectedPathRegex = $this->extract_resource_fixture_path_regex($fixture);
        $dispatchedRequest = $this->history[0]['request'];
        $this->assertRegExp($expectedPathRegex, $dispatchedRequest->getUri()->getPath());
    }

    
    public function testCreditorsUpdate()
    {
        $fixture = $this->loadJsonFixture('creditors')->update;
        $this->stub_request($fixture);

        $service = $this->client->creditors();
        $response = call_user_func_array(array($service, 'update'), (array)$fixture->url_params);

        $body = $fixture->body->creditors;
    
        $this->assertInstanceOf('\GoCardlessPro\Resources\Creditor', $response);

        $this->assertEquals($body->address_line1, $response->address_line1);
        $this->assertEquals($body->address_line2, $response->address_line2);
        $this->assertEquals($body->address_line3, $response->address_line3);
        $this->assertEquals($body->can_create_refunds, $response->can_create_refunds);
        $this->assertEquals($body->city, $response->city);
        $this->assertEquals($body->country_code, $response->country_code);
        $this->assertEquals($body->created_at, $response->created_at);
        $this->assertEquals($body->id, $response->id);
        $this->assertEquals($body->links, $response->links);
        $this->assertEquals($body->logo_url, $response->logo_url);
        $this->assertEquals($body->name, $response->name);
        $this->assertEquals($body->postal_code, $response->postal_code);
        $this->assertEquals($body->region, $response->region);
        $this->assertEquals($body->scheme_identifiers, $response->scheme_identifiers);
        $this->assertEquals($body->verification_status, $response->verification_status);
    

        $expectedPathRegex = $this->extract_resource_fixture_path_regex($fixture);
        $dispatchedRequest = $this->history[0]['request'];
        $this->assertRegExp($expectedPathRegex, $dispatchedRequest->getUri()->getPath());
    }

    
}
