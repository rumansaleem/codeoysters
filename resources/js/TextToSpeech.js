import axios from 'axios';

export default class TextToSpeech {
    constructor (key) {
        this.url = "https://texttospeech.googleapis.com/v1/text:synthesize?key=" + encodeURIComponent(key);
    }

    synthesize(text, language) {
        return new Promise((resolve, reject) => {
            let params = {
                input: {
                    text: text,
                },
                voice: {
                    languageCode: language
                },
                audioConfig: {
                    audioEncoding: "LINEAR16"
                }
            }
            axios.post(this.url, params).then(({data}) => {
                this.decodeBase64DataUri(data.audioContent)
                    .then(uri => {
                        resolve(uri)
                    })
                    .then(reject);
            }).catch(reject);
        })
    }

    decodeBase64DataUri(b64String, type = 'audio/wav') {
        let bytesArray = new Uint8Array(
            Array.from(atob(b64String)).map(char => char.charCodeAt(0))
        );

        return new Promise(resolve => {
            let reader = new FileReader();
            reader.addEventListener('load', () => resolve(reader.result));
            reader.addEventListener('error', (e) => reject(e));
            reader.readAsDataURL(new Blob([bytesArray], {type}));
            console.log("reading");
        });
    }

}
