import { createApp } from 'vue'
import { createVuetify } from 'vuetify'
import 'vuetify/styles'

import '@fortawesome/fontawesome-free/css/all.css'
import '@google/model-viewer'

import './style.css'
import './assets/overrides.css'

import App from './App.vue'
import router from './router'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import  GLTFModel  from 'vue-3d-loader'

const vuetify = createVuetify({
  components,
  directives,
})

createApp(App)
  .use(router)
  .use(vuetify)
  .component('gltf-model', GLTFModel)
  .mount('#app')