<template>
  <v-app>
    <div id="app">
      <h1>Sistema de Horários - UniFil</h1>
      <router-view />
    </div>
  </v-app>
  <div>
    <v-btn @click="abrirProfessores">Gerenciar Professores</v-btn>
    <Professores ref="popupProfessores" />
  </div>
</template>

<script>
export default {
  name: 'App',
  components: { Professores },
  methods: {
    abrirProfessores() {
      this.$refs.popupProfessores.abrirPopup()
    }
  }
}
import Professores from './Professores.vue'
</script>

<style scoped>
#app {
  font-family: Arial, sans-serif;
  text-align: center;
  margin-top: 50px;
}
</style>
