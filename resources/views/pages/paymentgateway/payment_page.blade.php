<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-20">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
            <h1 class="text-2xl font-bold text-center mb-4">Payment Page</h1>
            
            <!-- Form untuk Input Pembayaran -->
            <form method="POST" action="/process-payment">
                @csrf
                <div class="mb-6">
                    <label for="amount" class="block mb-2 text-sm text-gray-700">Amount</label>
                    <input type="number" id="amount" name="amount" class="w-full px-3 py-2 border rounded-md" required>
                </div>

                <div class="mb-6">
                    <label for="path" class="block mb-2 text-sm text-gray-700">Payment Path</label>
                    <select id="path" name="path" class="w-full px-3 py-2 border rounded-md" required>
                        <option value="/bri-virtual-account/v2/payment-code">BANK BRI</option>
                        <option value="/bni-virtual-account/v2/payment-code">BANK BNI</option>
                        <option value="/bca-virtual-account/v2/payment-code">BANK BCA</option>
                        <option value="/mandiri-virtual-account/v2/payment-code">BANK MANDIRI</option>



                    </select>
                </div>

                <button type="submit" class="w-full bg-green-500 text-white py-3 rounded-lg text-lg font-bold hover:bg-green-600">
                    Pay Now
                </button>
            </form>
        </div>
    </div>
</body>
</html>
