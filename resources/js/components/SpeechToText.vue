<template>
    <AudioRecorder @input="onAudio"></AudioRecorder>
</template>
<script>
import AudioRecorder from './AudioRecorder.vue';
import axios from 'axios';
export default {
    components: {AudioRecorder},
    props: {
        languageCode: {required: true},
        apiKey: {required: true}
    },
    methods: {
        onAudio(media) {
            this.getDataUrl(media.blob).then(url => {
                (new Audio(url)).play();
                let base64String = url.replace(/^data:audio\/flac;base64,/, '');
                this.getSpeech(base64String);
            });
        },
        getDataUrl(blob) {
            return new Promise(resolve => {
                let reader = new FileReader();
                reader.addEventListener('load', () => resolve(reader.result))
                reader.readAsDataURL(blob);
            });
        },
        getSpeech(flacBase64String) {
            axios.post('https://speech.googleapis.com/v1/speech:recognize?key=' + this.apiKey, {
                config: {
                    "encoding": "FLAC",
                    "sampleRateHertz": 48000,
                    "languageCode": this.languageCode,
                },
                audio: {
                    content: flacBase64String
                }
            }).then((response)=> {
                this.$emit('text', response.data.results);
            }).catch(({response}) => console.log(response));
        },
    }
}
</script>

