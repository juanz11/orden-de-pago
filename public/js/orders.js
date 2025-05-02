document.addEventListener('DOMContentLoaded', function() {
    const paymentTypeSelect = document.getElementById('payment_type');
    const percentageContainer = document.getElementById('payment_percentage_container');
    const relatedOrderContainer = document.getElementById('related_order_container');
    const percentageInput = document.getElementById('payment_percentage');
    const relatedOrderSelect = document.getElementById('related_order_id');
    const remainingText = document.getElementById('remaining_percentage_text');

    // Mostrar/ocultar campos según el tipo de pago
    paymentTypeSelect.addEventListener('change', function() {
        const isPartial = this.value === 'partial';
        percentageContainer.style.display = isPartial ? 'block' : 'none';
        relatedOrderContainer.style.display = isPartial ? 'block' : 'none';
        
        if (!isPartial) {
            percentageInput.value = '';
            relatedOrderSelect.value = '';
            remainingText.textContent = '';
        }
    });

    // Actualizar información cuando se selecciona una orden relacionada
    relatedOrderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remainingPercentage = selectedOption.dataset.remaining;
            remainingText.textContent = `Porcentaje disponible para pago: ${remainingPercentage}%`;
            percentageInput.max = remainingPercentage;
            if (parseFloat(percentageInput.value) > parseFloat(remainingPercentage)) {
                percentageInput.value = remainingPercentage;
            }
        } else {
            remainingText.textContent = '';
            percentageInput.max = 100;
        }
    });

    // Validar que el porcentaje no exceda el máximo permitido
    percentageInput.addEventListener('input', function() {
        const selectedOption = relatedOrderSelect.options[relatedOrderSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remainingPercentage = parseFloat(selectedOption.dataset.remaining);
            const currentValue = parseFloat(this.value);
            if (currentValue > remainingPercentage) {
                this.value = remainingPercentage;
            }
        }
    });
});
