<template>
    <div class="flex flex-col items-center">   
        <div v-if="!explicitSelection">
            <p class="my-1" v-for="instruction in instructions" :key="instruction" v-text="instruction"></p>
        </div>
        <ul class="list-reset">
            <li class="my-1" v-for="(message,i) in messages" :key="i" v-text="message"></li>
        </ul>
        <div class="mt-4 px-3 inline-flex items-center border rounded-full">
            <input type="text" class="py-2" @keydown.enter="submit" v-model="newMessage">
            <speech-to-text @text="onRecognition" :api-key="apiKey" :language-code="selectedLanguage"></speech-to-text>
        </div>
        <button @click="speak">Speak</button>
    </div>
</template>
<script>
import SpeechToText from './SpeechToText';
import TextToSpeech from '../TextToSpeech';
export default {
    props: {
        apiKey: {required: true}
    },
    components: {SpeechToText, TextToSpeech},
    data() {
        return {
            messages: [],
            newMessage: '',
            selectedLanguage: 'en-IN',
            instructions: [
                "Speak the name of your preferred language.",
                "अपनी पसंदीदा भाषा का नाम बोलें।",
                "आपल्या प्राधान्य भाषेचे नाव सांगा.",
                "તમારી પસંદગીની ભાષાના નામ બોલો.",
                "আপনার পছন্দের ভাষা নাম বলুন।",
                "మీ ప్రాధాన్య భాష యొక్క పేరును మాట్లాడండి.",
                "اپنی ترجیحی زبان کا نام بولیں",
                "ನಿಮ್ಮ ಆದ್ಯತೆಯ ಭಾಷೆಯ ಹೆಸರನ್ನು ಮಾತನಾಡಿ.",
                "உங்கள் விருப்பமான மொழியின் பெயரைப் பேசுங்கள்.",
                "നിങ്ങളുടെ ഇഷ്ട ഭാഷയുടെ പേര് പറയുക.",
            ],
            languages: {
                'bengali':'bn-IN',
                'english': 'en-IN',
                'gujarati':'gu-IN',
                'gujrati':'gu-IN',
                'kanad':'kn-IN',
                'kannad':'kn-IN',
                'kannada':'kn-IN',
                'malyalam':'ml-IN',
                'malayalam':'ml-IN',
                'marathi':'mr-IN',
                'tamil':'ta-IN',
                'telugu':'te-IN',
                'urdu': 'ur-IN',
                'hindi': 'hi-IN',
            },
            explicitSelection: false,
        }
    },
    methods: {
        speak(text) {
            let langCodes = {
                "en":'en_mohita',
                "hi":'hi_mohita',
                "mr": "mr_mohita",
                "kn": "kn_dipa",
                "gu": "gu_rashmi",
            }
            let key = this.selectedLanguage.split('-')[0];
            if(!langCodes.hasOwnProperty(key)) {
                return;
            }
            let lang = langCodes[key];
            let url = `http://ivrapi.indiantts.co.in/tts?type=indiantts&text=${text}&api_key=588af6d0-3b52-11e9-8795-0b4320c1e23d&user_id=56791&action=play&numeric=hcurrency&lang=${lang}`
            let audio = new Audio();
            audio.addEventListener('canplaythrough', () => audio.play());
            audio.src = url;
            audio.load();
        },
        onRecognition(results) {
            let text = results[0].alternatives[0].transcript;
            let key = text.toLowerCase().trim();
            if(!this.explicitSelection && this.languages.hasOwnProperty(key)) {
                this.selectedLanguage = this.languages[key];
                this.explicitSelection = true;
                this.translate('Your selected language is ' + text, this.selectedLanguage.split('-')[0])
                    .then(({data}) => {
                        let text = data.data.translations[0].translatedText
                        this.messages.push(text);
                        this.speak(text);
                    });
                return;
            }
            this.messages.push(text);
            if(this.selectedLanguage == 'en-IN') {
                this.newMessage = text;
                this.submit();
                return;
            } 

            this.translate(text).then(({data}) => {
                this.newMessage = data.data.translations[0].translatedText;
                this.submit();
            });
        },
        submit() {
            axios.get('/dialogflow', { params:{
                text: this.newMessage
            }}).then(response => {
                this.translate(response.data, this.selectedLanguage.split('-')[0])
                    .then(({ data })=> {
                        let text = data.data.translations[0].translatedText;
                        this.messages.push(text);
                        this.speak(text);
                    })
                this.newMessage = ''
            }).catch(error => console.log(error.response));
        },
        translate(text, target = 'en') {
            let params = {
                key: this.apiKey,
                q: text,
                format: 'text',
                target,
            };
            return axios.post(`https://translation.googleapis.com/language/translate/v2`, null, {params})
        },
    }
}
</script>
