<?php
namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ThirdPartyIsbnSearchController extends AbstractController
{
    // ToDo: Add authentication.

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    #[Route('/search/proxy/jp_ndl', name: 'search_jp_ndl', methods: ['GET'])]
    #[Cache(public: true, maxage: 604800, mustRevalidate: false)]
    public function searchJpNdl(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $isbn = $request->query->get('isbn');
        if (!$isbn) {
            return $this->json(['error' => 'ISBN parameter is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!preg_match('/^[0-9]{13}$/', $isbn)) {
            return $this->json(['error' => 'Invalid ISBN format'], Response::HTTP_BAD_REQUEST);
        }

        $url = "https://ndlsearch.ndl.go.jp/api/sru?operation=searchRetrieve&version=1.2&recordSchema=dcndl&onlyBib=true&recordPacking=xml&query=isbn=%22$isbn%22%20AND%20dpid=iss-ndl-opac";

        $response = $this->client->request('GET', $url);
        $content = $response->getContent();

        $xml = simplexml_load_string($content);
        if ($xml === false) {
            return $this->json(['error' => 'Invalid XML'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $ns = $xml->getNamespaces(true);

        $recordNum = $xml->numberOfRecords;
        if ($recordNum == 0) {
            return $this->json(['error' => 'No records found'], Response::HTTP_NOT_FOUND);
        }

        $toret = [];

        foreach($xml->records as $recordHost)
        {
            $record_data = $recordHost->record->recordData;
            $record_data->registerXPathNamespace('rdf', $ns['rdf']);
            $rdf = $record_data->xpath('.//rdf:RDF')[0];
            $rdf->registerXPathNamespace('rdfs', $ns['rdfs']);
            $rdf->registerXPathNamespace('dc', $ns['dc']);
            $rdf->registerXPathNamespace('dcterms', $ns['dcterms']);
            $rdf->registerXPathNamespace('dcndl', $ns['dcndl']);
            $rdf->registerXPathNamespace('foaf', $ns['foaf']);

            $bookinfo = [];

            $title = $rdf->xpath('.//dcterms:title')[0] ?? '';
            if ($title !== '')
                $bookinfo['title'] = (string) $title;

            $titleRead = $rdf->xpath('.//dc:title/rdf:Description/dcndl:transcription')[0] ?? '';
            if ($titleRead !== '')
                $bookinfo['titleRead'] = (string) $titleRead;

            $titleRead = $rdf->xpath('.//dcndl:volume/rdf:Description/dcndl:transcription')[0] ?? '';
            if ($titleRead !== '')
                $bookinfo['titleRead'] .= ' ' . ((string) $titleRead);

            $publisher = $rdf->xpath('.//dcterms:publisher/foaf:Agent/foaf:name')[0] ?? '';
            if ($publisher !== '')
                $bookinfo['publisher'] = (string)$publisher;

            $toret[] = $bookinfo;
        }

        return $this->json($toret);
    }
}
