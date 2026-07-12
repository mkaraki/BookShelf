window.addEventListener('load', function () {
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
                Quagga.stop();
                window.opener.applyCodeReaderResult(cv);
                window.close();
            } else if (cl >= 4 && cv.startsWith('0')) {
                // BookShelf Code
                Quagga.stop();
                window.opener.applyCodeReaderResult(cv);
                window.close();
            }
            else
            {
                return;
            }
            Quagga.stop();
        });
        Quagga.start();
    });
});
