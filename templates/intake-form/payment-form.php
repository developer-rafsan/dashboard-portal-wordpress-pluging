<?php
defined('ABSPATH') or die('No direct access.');
?>

<!-- payment form -->
<div id="payment_content" style='z-index: 99999'
    class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center">

    <div class="w-full max-w-lg bg-white rounded-lg shadow-xl p-6 flex items-center justify-center hidden"
        id="loading-spinner">
        <div class="spinner"></div>
    </div>

    <div id="payment_fill" class="w-full max-w-lg bg-white rounded-lg shadow-xl p-6 hidden">
        <!-- Header -->
        <div class="border-b pb-4 mb-4 flex justify-center items-center">
            <div class="text-xl font-bold text-blue-500">Payment</div>
        </div>

        <!-- Info Section -->
        <div class="mb-4">
            <p class="text-gray-500 mb-1">
                <span class="font-normal">Full Name:</span> <span class="font-bold text-gray-700"
                    id="payment-case-name"></span>
            </p>
            <p class="text-gray-500 mb-1">
                <span class="font-normal">Case ID:</span> <span class='font-bold text-gray-700'
                    id="payment-case-id"></span>
            </p>
            <p class="text-gray-500">
                <span class="font-normal">Total Amount:</span> <span class="font-bold text-gray-700"
                    id="payment-amount"></span>
            </p>
        </div>

        <!-- Stripe Element -->
        <form id="payment-form" class="space-y-4">
            <div id="payment-element" class="border rounded-md p-3"></div>

            <div class="flex justify-end gap-3">
                <button type="submit" id="submit"
                    class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 transition w-full">
                    Pay
                </button>
            </div>
        </form>
    </div>
</div>


<style>
.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>


<script>
function initializePaymentForm() {
    const stripe = Stripe(ajax_object.publishable_key);
    const payment_content = document.querySelector('#payment_content');
    const payment_btn = document.querySelectorAll('.payment-btn');

    let elements;

    // Payment button handlers
    payment_btn.forEach(action => {
        action.addEventListener('click', () => {
            const data = {
                id: action.dataset.caseId,
                amount: action.dataset.caseAmount,
                name: action.dataset.caseName
            }
            document.getElementById('loading-spinner').classList.remove('hidden');
            payment_content.querySelector("#payment-case-id").textContent = data.id;
            payment_content.querySelector("#payment-case-name").textContent = data.name;
            payment_content.querySelector("#payment-amount").textContent = `$${data.amount}`;
            payment_content.classList.remove('hidden');
            initializeStripe(data.amount, data.id);
        });
    });

    // Close modal when clicking outside
    if (payment_content) {
        payment_content.addEventListener("click", function(e) {
            if (e.target === payment_content) {
                payment_content.classList.add("hidden");
            }
        });
    }

    // Initialize Stripe payment
    async function initializeStripe(amount, caseID) {
        try {
            const response = await fetch(ajax_object.ajax_url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    action: "handle_stripe_payment",
                    security: ajax_object.nonce,
                    amount: amount,
                    caseid_id: caseID,
                    type: 'case_payment',
                    customer_email: 'customer@example.com'
                })
            });

            const {
                success,
                data
            } = await response.json();

            if (!success) {
                console.error('Failed to initialize payment:', data);
                return;
            }

            const appearance = {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#0570de',
                    colorBackground: '#ffffff',
                    colorText: '#30313d',
                    fontFamily: 'Arial, sans-serif',
                    borderRadius: '8px',
                },
                rules: {
                    '.Input': {
                        padding: '12px',
                        border: '1px solid #ccc',
                        borderRadius: '6px',
                    },
                    '.Label': {
                        fontSize: '14px',
                        fontWeight: '500',
                        color: '#333',
                    }
                }
            };

            // Use the client secret returned from the server
            const clientSecret = data.client_secret;
            elements = stripe.elements({
                clientSecret,
                appearance
            });

            const paymentElement = elements.create('payment', {
                layout: {
                    type: 'tabs',
                    defaultCollapsed: true
                }
            });

            paymentElement.mount('#payment-element');
            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('payment_fill').classList.remove('hidden');

        } catch (error) {
            console.error('Error initializing Stripe:', error);
        }
    }
    
    document.getElementById('payment-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const form = event.currentTarget;
    const submitBtn = form.querySelector('button');
    const caseId = document.querySelector('#payment-case-id')?.textContent?.trim();
    const paymentAmount = document.querySelector('#payment-amount')?.textContent?.trim().replace('$', '');
    const notificationContainer = document.querySelector('.notification-container');
    const notificationTitle = notificationContainer.querySelector('#notification-title');

    // Utility: Change button state
    const setButtonState = (text, disabled) => {
        submitBtn.textContent = text;
        submitBtn.disabled = disabled;
        submitBtn.style.opacity = disabled ? '0.5' : '1';
    };

    try {
        setButtonState('Processing...', true);

        // Confirm Stripe payment
        const { paymentIntent, error } = await stripe.confirmPayment({
            elements,
            redirect: 'if_required'
        });

        if (error) {
            notificationTitle.textContent = `Payment Failed: ${error.message || 'Unknown error'}`;
            throw new Error(error.message);
        }

        notificationTitle.textContent = 'Payment Successful';

        // Debug: Log the data being sent
        console.log('Updating payment status with:', {
            caseId: caseId,
            paymentAmount: paymentAmount
        });

        // Update backend with payment details
        const response = await fetch(ajax_object.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'update_case_payment_status',
                nonce: ajax_object.nonce,
                case_id: caseId,
                payment_amount: paymentAmount
            })
        });

        const result = await response.json();
        if (!result.success) {            
            throw new Error(result.data || 'Failed to update payment status');
        }

        console.log(result);
        

    } catch (err) {
        console.error('Payment Error:', err);
    } finally {
        // Restore button and show notification
        setButtonState('Pay Now', false);
        notificationContainer.classList.add('active');
        setTimeout(() => {
            // Optional: Reload after success
            window.location.reload();
        }, 1500);
    }
});


}


// Start initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePaymentForm);
} else {
    initializePaymentForm();
}
</script>