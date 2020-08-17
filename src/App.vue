<template>
  <div id="filechecksum" class="section">
    <h2>File Checksum</h2>
    <Scanning />
    <hr/>
    <b-container fluid>
      <b-row>
        <b-col> <ChecksumGeneration /> </b-col>
        <b-col> <PhotoMetaData /> </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import Scanning from './components/Scanning.vue'
import ChecksumGeneration from './components/ChecksumGeneration.vue'
import PhotoMetaData from './components/PhotoMetaData.vue'

export default {
  name: 'App',
  components: {
    Scanning,
    ChecksumGeneration,
    PhotoMetaData
  }
}
</script>

<style lang="scss" scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
::v-deep {
  @import 'node_modules/bootstrap/scss/bootstrap';
  @import 'node_modules/bootstrap-vue/src/index.scss';
}

</style>
