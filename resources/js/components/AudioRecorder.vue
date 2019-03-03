<template>
    <button @click="toggleMic">
        <svg viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="1em" :class="{'text-red' : isRecording}">
            <path d="M9,18 L9,16.9379599 C5.05368842,16.4447356 2,13.0713165 2,9 L4,9 L4,9.00181488 C4,12.3172241 6.6862915,15 10,15 C13.3069658,15 16,12.314521 16,9.00181488 L16,9 L18,9 C18,13.0790094 14.9395595,16.4450043 11,16.9378859 L11,18 L14,18 L14,20 L6,20 L6,18 L9,18 L9,18 Z M6,4.00650452 C6,1.79377317 7.79535615,0 10,0 C12.209139,0 14,1.79394555 14,4.00650452 L14,8.99349548 C14,11.2062268 12.2046438,13 10,13 C7.790861,13 6,11.2060545 6,8.99349548 L6,4.00650452 L6,4.00650452 Z" fill="currentColor"></path>
        </svg> 
    </button>
</template>
<script>
export default {
    props: {
        maxDuration: {default: 15}
    },
    data() {
        return {
            isRecording: false,
            bufferSize: 4096,
            flacData: {
                bps: 16,
                channels: 1,
                compression: 5,
                samplerate: 48000,
            },
            audioContext: null,
            audioInput: null,
            recorder: null,
            encoder: null,
        }
    },
    methods: {
        toggleMic() {
            if(!this.isRecording) {
                this.record();
            } else {
                this.stopRecording();
            }
        },
        record() {
            this.encoder = new Worker('/encoder.js');
            this.encoder.addEventListener("message", this.onEncoderDone);
            this.getMediaStream({audio: true, video: false})
                .then((stream) => this.gotMediaStream(stream));

            setTimeout(this.stopRecording, this.maxDuration*1000);
        },
        getMediaStream(options) {
            return navigator.mediaDevices.getUserMedia(options);
        },
        gotMediaStream(stream) {
            this.stream = stream;
            this.audioContext = new AudioContext;
            this.audioInput = this.audioContext.createMediaStreamSource(stream);

            this.isRecording = true;
            this.recorder = this.audioInput.context.createScriptProcessor(this.bufferSize, 1, 1);

            if(!this.flacData.samplerate) {
                this.flacData.samplerate = this.audioContext.sampleRate;
            }

            this.encoder.postMessage({cmd: "init", config: this.flacData});

            this.recorder.addEventListener('audioprocess', this.onAudioProcess);

            this.audioInput.connect(this.recorder);
            this.recorder.connect(this.audioContext.destination)

        },
        onAudioProcess(event) {
            if (!this.isRecording)
				return;
			let channelData  = event.inputBuffer.getChannelData(0);
            this.encoder.postMessage({ cmd: 'encode', buf: channelData});
        },
        stopRecording() {
            if (!this.isRecording) {
                return;
            }

            this.stream.getAudioTracks().forEach(track => track.stop);
            this.isRecording = false;
            this.encoder.postMessage({ cmd: 'finish' });

            this.audioInput.disconnect();
            this.recorder.disconnect();
            this.audioInput = this.recorder = null;
        },
        onEncoderDone(event) {
            if (event.data.cmd == 'end') {
                this.$emit('input', {blob: event.data.buf, data: this.flacData});
                this.encoder.terminate();
                this.encoder = null;
            } else {
                console.log(`Unhandled Event from web worker '${event.data.cmd}'`)
            }
        }
    }
}
</script>

