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
            readers: ["ean_reader", "code_128_reader"]
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

            if (cl === 13 && cv.startsWith('192')) // Japan Book JAN Code (should ignored)
            {
                return;
            }
            else if (cl === 13 && (cv.startsWith('978') || cv.startsWith('979'))) {
                // ISBN-13
                location.href = 'isbn.php?isbn=' + cv;
            } else if (cl >= 4 && cv.startsWith('0')) {
                // BookShelf Code
                location.href = 'jump.php?code=' + cv;
            }
            else
            {
                return;
            }
            Quagga.stop();
        });
        Quagga.start();
    });
</script>
<?php require_once __DIR__ . '/../partial/page-end.php'; ?>