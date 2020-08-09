<template>
  <div id="filechecksum">
  <FileChart />
  
  </div>
</template>

<script>
import FileChart from './components/FileChart.vue'

export default {
  name: 'App',
  components: {
    FileChart,
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
