import '@fortawesome/fontawesome-free/css/all.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { aliases, fa } from 'vuetify/iconsets/fa'
import { md } from 'vuetify/iconsets/md'

const vuetify = createVuetify({
    icons: {
        defaultSet: 'fa',
        sets: {
        fa: {
            component: 'v-icon',
        },
        },
    },
})
  
export default vuetify