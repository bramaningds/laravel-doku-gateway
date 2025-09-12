<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <input id="invoice_id" type="text" value="10000" placeholder="Invoice ID, eg 1, 2...">
    <input id="pay-button" type="button" value="Bayar">

    <script>
        document.querySelector('#pay-button').onclick = function() {
            const invoiceId = document.querySelector('#invoice_id').value;
            console.info(invoiceId);
            window.open(`{{ env('APP_URL') }}/payment/${invoiceId}`, '_blank').focus();
        }
    </script>
</body>

</html>
