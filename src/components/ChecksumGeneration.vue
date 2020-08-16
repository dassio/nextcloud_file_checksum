<template>
  <div id="filechart">
      <button id="generation" class="disabled" v-on:click="generateChecksum" >Generate Checksum</button>
  </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { bus } from '../main';

export default {
  name: "FileChart",

  data: function () {
    return {
      fileListJson: null,
    };
  },

  created (){
    bus.$on('sanningFinished', (fileListJson) => {
      this.fileListJson = fileListJson;
    })
  },

  methods: {
    /*
    ==========================================================
    Function : start scanning 
    ==========================================================
    */
    generateChecksum: function () {
      this.buttonFunctionType = this.buttonFunctionCancel;
      axios
        .get(generateUrl("apps/filechecksum/api/statistic/startscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
        });
      this.intervalId = setInterval(this.checkingScanningProgress, this.statusCheckInterval);
    },
  components: {
    MyAnimatedNumber
  },
};
</script>

<style lang="scss">
</style>
