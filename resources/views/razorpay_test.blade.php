<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function payNow() {
        var options = {
            "key": "rzp_test_TNO3UezXqjM11G", // Razorpay key from server
            "amount": "100", // Amount in paise
            "currency": "INR",
            "name": "Merchant Name",
            "description": "Purchase Description",
            "order_id": "order_OMFffJfhm33I2e", // Order ID from server
            "handler": function (response){
                // Handle the payment response
                console.log(response);

                // Send this response to your server
                axios.post('/api/razorpay-payment', {
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature
                })
                .then(res => {
                    console.log(res.data);
                    // Handle success response
                })
                .catch(err => {
                    console.error(err.response.data);
                    // Handle error response
                });
            },
            "prefill": {
                "name": "Customer Name",
                "email": "customer@example.com",
                "contact": "9999999999"
            },
            "notes": {
                "address": "Customer Address"
            },
            "theme": {
                "color": "#F37254"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    }
</script>

<button onclick="payNow()">Pay Now</button>
