<div id="receiptModal" class="modal">
    <div class="modal-content">

        <span class="close" onclick="closeReceiptModal()">&times;</span>

        <h2>Transaction Summary</h2>

        <div class="receipt-summary">
            <p><strong>Total:</strong> ₱<span id="summaryTotal">0.00</span></p>
            <p><strong>Paid:</strong> ₱<span id="summaryPaid">0.00</span></p>
            <p><strong>Change:</strong> ₱<span id="summaryChange">0.00</span></p>
        </div>

        <div class="receipt-actions">
            <button type="submit" name="checkout" class="confirm-btn">
                Confirm & Save
            </button>

            <button type="button" onclick="window.print()" class="print-btn">
                Print Receipt
            </button>

            <button type="button" onclick="closeReceiptModal()" class="cancel-btn">
                Close
            </button>
        </div>

    </div>
</div>
