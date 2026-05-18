import { createApp } from 'vue'

import App from './App.vue'
import { setupPlugins } from './plugins'

import '@/assets/index.css'
import '@/assets/scrollbar.css'
import 'vue-sonner/style.css' // vue sonner style

import '@/utils/env'

async function bootstrap() {
  const app = createApp(App)

  await setupPlugins(app)

  app.mount('#app')
}

void bootstrap()
