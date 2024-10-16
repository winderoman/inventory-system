// Asegúrate de que este script se cargue después de que el DOM esté listo
document.addEventListener('DOMContentLoaded', (event) => {
    const searchInput = document.querySelector('input[name="globalSearch"]');
    const voiceButton = document.createElement('button');
    voiceButton.innerHTML = '🎤'; // Emoji de micrófono
    voiceButton.style.marginLeft = '10px';
    searchInput.parentNode.insertBefore(voiceButton, searchInput.nextSibling);

    voiceButton.addEventListener('click', function() {
        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'es-ES'; // Ajusta esto al idioma que necesites

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            searchInput.value = transcript;
            // Aquí puedes agregar código para enviar la búsqueda automáticamente si lo deseas
        };

        recognition.start();
    });
});