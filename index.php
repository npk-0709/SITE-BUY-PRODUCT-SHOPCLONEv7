<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Mua Hàng Shop Clone V7</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

    <div class="container mt-5" style="width: 60%;">
        <h2 class="text-center">BUY PRODUCT SHOPCLONEv7</h2>
        <form id="buyForm">
            <div class="mb-3 d-flex">
                <label for="domain" class="form-label">Domain: </label>
                <input type="text" class="form-control" id="domain" required>
            </div>
            <div class="mb-3 d-flex">
                <label for="apiKey" class="form-label">ApiKey: </label>
                <input type="text" class="form-control" id="apiKey" required>
                <button type="button" class="btn btn-primary" id="checkLiveApiKey" style="width: 50%;">Check Live ApiKey</button>
            </div>
            <input type="text" id="balance" style="display: none;" class="form-control">
            <br>

            <div class="mb-3 d-flex">
                <label for="productId" class="form-label">ProductID: </label>
                <input type="text" class="form-control" id="productId" required>
            </div>
            <div class="mb-3 d-flex">
                <label for="amount" class="form-label">Amount: </label>
                <input type="number" class="form-control" id="amount" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Response message as a textarea -->
        <div class="mt-4">
            <div class="alert alert-info" role="alert">
                <strong>Response Message:</strong>
                <textarea id="responseMessage" class="form-control" rows="2" style="width: 100%;"></textarea>
            </div>
            <div class="mt-4">
                <div class="alert alert-info" role="alert">
                    <strong>Response Data:</strong>
                    <textarea id="responseData" class="form-control" rows="4" style="width: 100%;"></textarea>
                </div>
            </div>

            <!-- Bootstrap JS and jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                $(document).ready(function() {

                    const proxyUrl = "proxy.php?url=";

                    $("#checkLiveApiKey").click(function() {
                        checkLiveApiKey();
                    });

                    function checkLiveApiKey() {
                        document.getElementById("balance").style.display = "block";
                        document.getElementById("balance").value = "Checking...";
                        var domain = $("#domain").val();
                        var apiKey = $("#apiKey").val();
                        $.ajax({
                            url: proxyUrl + domain + '/api/profile.php?api_key=' + apiKey,
                            type: 'GET',
                            success: function(response) {
                                response = JSON.parse(response);
                                var message = response.msg || "Unknown response";
                                var status = response.status || "error";
                                if (status === 'success') {
                                    $("#balance").val(response.data.username + " - " + response.data.money);
                                } else {
                                    $("#balance").val(message);
                                }
                            }
                        });
                    }

                    $("#buyForm").on("submit", function(e) {
                        e.preventDefault();
                        var domain = $("#domain").val();
                        var apiKey = $("#apiKey").val();
                        var productId = $("#productId").val();
                        var amount = $("#amount").val();
                        var coupon = $("#coupon").val();
                        var data = {
                            action: 'buyProduct',
                            id: productId,
                            amount: amount,
                            coupon: coupon,
                            api_key: apiKey
                        };
                        $.ajax({
                            url: proxyUrl + domain + '/api/buy_product',
                            type: 'POST',
                            data: data,
                            success: function(response) {
                                response = JSON.parse(response);
                                var message = response.msg || "Unknown response";
                                var status = response.status || "error";
                                if (status === 'success') {
                                    var responseText = message + "\nTransaction ID: " + response.trans_id + "\nData:\n";
                                    var responseData = "";
                                    response.data.forEach(function(item) {
                                        responseData += item + "\n";
                                    });
                                    $('#responseMessage').val(responseText).removeClass("is-invalid").addClass("is-valid");
                                    $('#responseData').val(responseData).removeClass("is-invalid").addClass("is-valid");
                                } else {
                                    $('#responseMessage').val(message).removeClass("is-valid").addClass("is-invalid");
                                    $('#responseData').val("").removeClass("is-valid").addClass("is-invalid");
                                }
                            },
                            error: function() {
                                $('#responseMessage').val('Error: Unable to complete the purchase.').removeClass("is-valid").addClass("is-invalid");
                            }
                        });
                    });
                });
            </script>

</body>

</html>
