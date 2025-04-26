<?php
require_once __DIR__ . '/../../internal/auth.php';

$isbn = $_GET['isbn'] ?? '';

if (!preg_match('/^[0-9]{13}$/', $isbn)) {
    http_response_code(400);
    die('not a valid isbn');
}

$url = "https://ndlsearch.ndl.go.jp/api/sru?operation=searchRetrieve&version=1.2&recordSchema=dcndl&onlyBib=true&recordPacking=xml&query=isbn=%22$isbn%22%20AND%20dpid=iss-ndl-opac";

$api_res = file_get_contents($url);

$xml = simplexml_load_string($api_res);
if ($xml === false) {
    http_response_code(500);
    die("Invalid response from NDL server. XML parse error.");
}

$ns = $xml->getNamespaces(true);

$recordNum = $xml->numberOfRecords;
if ($recordNum == 0) {
    http_response_code(404);
    die("[]");
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

header('Content-type: application/json');
print(json_encode($toret));