document.addEventListener('DOMContentLoaded', function() {
    const isPartialPaymentSelect = document.getElementById('is_partial_payment');
    const partialPaymentFields = document.getElementById('partial_payment_fields');
    const relatedOrderSelect = document.getElementById('related_order_id');
    const paymentPercentageInput = document.getElementById('payment_percentage');

    // Mostrar/ocultar campos de pago parcial
    isPartialPaymentSelect.addEventListener('change', function() {
        partialPaymentFields.style.display = this.value === '1' ? 'block' : 'none';
        if (this.value === '0') {
            relatedOrderSelect.value = '';
            paymentPercentageInput.value = '';
        }
    });

    // Actualizar máximo porcentaje permitido cuando se selecciona una orden
    relatedOrderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption) {
            const remainingPercentage = selectedOption.dataset.remaining;
            paymentPercentageInput.max = remainingPercentage;
            paymentPercentageInput.placeholder = `Máximo ${remainingPercentage}%`;
        }
    });

    // Validar que el porcentaje no exceda el máximo permitido
    paymentPercentageInput.addEventListener('input', function() {
        const selectedOption = relatedOrderSelect.options[relatedOrderSelect.selectedIndex];
        if (selectedOption) {
            const remainingPercentage = parseFloat(selectedOption.dataset.remaining);
            const currentValue = parseFloat(this.value);
            if (currentValue > remainingPercentage) {
                this.value = remainingPercentage;
            }
        }
    });
});
