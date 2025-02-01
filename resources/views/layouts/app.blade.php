<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('show-pdf', (data) => {
            // Crear blob del PDF
            const byteCharacters = atob(data.pdf);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: 'application/pdf' });

            // Crear URL del blob
            const url = window.URL.createObjectURL(blob);

            // Abrir PDF en nueva pestaña
            window.open(url, '_blank');

            // Limpiar URL después de un momento
            setTimeout(() => {
                window.URL.revokeObjectURL(url);
            }, 100);
        });
    });
</script> 