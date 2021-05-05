<?php 

namespace App\tests\Controller;

use App\Tests\CustomWebTestCase;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MatchControllerTest extends CustomWebTestCase
{
    private $client;

    public function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = $this->logIn();
    }

    public function testIndex()
    {   
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}