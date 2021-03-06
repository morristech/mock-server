<?php declare(strict_types=1);

namespace EdmondsCommerce\MockServer;

use EdmondsCommerce\MockServer\Testing\SetsUpMockServerBeforeClassTrait;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

/**
 * Class MockServerTest
 * This class is purely for ensuring that the test methods work on the MockServerTrait trait
 *
 */
class MockServerTest extends TestCase
{
    use SetsUpMockServerBeforeClassTrait;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @skip
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testItWillHandleARoutingFile()
    {
        $url = static::$mockServer->getUrl('/routed');

        $client   = new Client();
        $response = $client->request('GET', $url);
        $html     = $response->getBody();

        $this->assertEquals('Routed', $html);
    }

    /**
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testItWillHandleFriendlyUrls()
    {

        $url = static::$mockServer->getUrl('/admin');

        $client   = new Client();
        $response = $client->request('GET', $url);
        $html     = $response->getBody();

        $this->assertEquals('Admin Login', $html);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws \Exception
     */

    public function testItWillClearTheRequestOnStart()
    {
        $requestFile = MockServer::getLogsPath().'/'.MockServer::REQUEST_FILE;
        touch($requestFile);

        static::$mockServer->startServer();
        $contents = file_get_contents($requestFile);
        $this->assertEmpty($contents, 'request file contains: '.$contents);
    }

    public function testItServesDownloadRoutes()
    {
        $url      = static::$mockServer->getUrl('/download');
        $client   = new Client();
        $buffer   = fopen('php://temp', 'w');
        $response = $client->request(
            'GET',
            $url,
            [
                'save_to'     => $buffer,
                'synchronous' => true,
            ]
        );
        $this->assertEquals(
            'attachment; filename=downloadfile.extension',
            current($response->getHeader('Content-Disposition'))
        );
        rewind($buffer);
        $contents = fread($buffer, 9999);
        $this->assertNotEmpty($contents);
        $this->assertEquals('this is a download file', trim($contents));
        fclose($buffer);
    }

    public function testItServesStaticJsonRoutes()
    {
        $jsonFile = __DIR__.'/MockServer/files/jsonfile.json';
        $url      = static::$mockServer->getUrl('jsonfile.json');
        $client   = new Client();
        $response = $client->request('GET', $url, ['synchronous' => true]);
        $this->assertEquals('application/json', current($response->getHeader('Content-Type')));
        $this->assertStringEqualsFile($jsonFile, $response->getBody()->getContents());
    }
}
