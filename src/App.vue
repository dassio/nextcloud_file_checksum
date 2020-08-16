<template>
  <div id="filechecksum" class="section">
  <h2>File Checksum</h2>
  <FileChart />
  </div>
</template>

<script>
import Scanning from './components/Scanning.vue'
import ChecksumGeneration from './components/ChecksumGeneration.vue'

export default {
  name: 'App',
  components: {
    Scanning,
    ChecksumGeneration
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
