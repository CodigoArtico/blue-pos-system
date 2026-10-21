<audio id="audio-success">
    <source src="{{ asset('sound/success.mp3') }}" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<audio id="audio-alert">
    <source src="{{ asset('sound/alert.mp3') }}" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<audio id="audio-error">
    <source src="{{ asset('sound/error.mp3') }}" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<audio id="audio-notification">
    <source src="{{ asset('sound/notification.mp3') }}" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<script>
    // Reproducir sonido de éxito
    function reproducirSonidoSuccess() {
        let audioElement = document.getElementById('audio-success');
        if (audioElement) {
            audioElement.play()
                .then(() => console.log('Success audio is playing.'))
                .catch(error => console.error('Error playing success audio:', error));
        } else {
            // console.error('No se pudo encontrar el elemento de audio con ID "audio-success".');
        }
    }

    // Reproducir sonido de alerta (error, advertencia)
    function reproducirSonidoAlert() {
        let audioElement = document.getElementById('audio-alert');
        if (audioElement) {
            audioElement.play()
                .then(() => console.log('Alert audio is playing.'))
                .catch(error => console.error('Error playing alert audio:', error));
        } else {
            // console.error('No se pudo encontrar el elemento de audio con ID "audio-alert".');
        }
    }

    // Reproducir sonido de error
    function reproducirSonidoError() {
        let audioElement = document.getElementById('audio-error');
        if (audioElement) {
            audioElement.play()
                .then(() => console.log('Success audio is playing.'))
                .catch(error => console.error('Error playing success audio:', error));
        } else {
            // console.error('No se pudo encontrar el elemento de audio con ID "audio-error".');
        }
    }
</script>
