<?php
require_once __DIR__ . '/../partial/page-head.php';
?>

<div id="prv"></div>

<script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js" integrity="sha256-JDVyLQRqvRSTL/6WaPud93JXpfEdW11zwjqhoNgkGXc=" crossorigin="anonymous"></script>
<script>
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#prv')
        },
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        decoder: {
            readers: ["ean_reader"]
        },
        locate: true,
    }, function(err) {
        if (err) {
            console.log(err);
            return
        }
        console.log("Initialization finished. Ready to start");
        Quagga.onDetected((r) => {
            console.log(r);

            const cl = r.codeResult.code.length;
            const cv = r.codeResult.code;

            if (cl == 13 && cv.startsWith('192')) // Japan Book JAN Code (should ignored)
            {
                return;
            }

            if (cl == 13 && cv.startsWith('97')) {
                // ISBN-13
                location.href = 'isbn.php?isbn=' + r.codeResult.code;
            } else if (cl > 3 && cv.startsWith('0')) {
                // BookShelf Code
                //location.href = 'shelf.php?code=' + r.codeResult.code;
            }
            Quagga.stop();
        });
        Quagga.start();
    });
</script>
<?php require_once __DIR__ . '/../partial/page-end.php'; ?>