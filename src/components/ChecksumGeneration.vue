<template>
  <div id="filechart">
    <b-button disabled variant="primary" class="mx-auto" id="generateChecksum" v-on:click="generateChecksum" ref="generateChecksum">Generate Checksum</b-button>
    <canvas id="fileChecksumPercentage" ref="fileChecksumPercentage"></canvas>
  </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import { bus } from '../main';
import Chart from 'chart.js';

export default {
  name: "FileChart",

  data: function () {
    return {
      fileListJson: null,
    };
  },

  mounted: function(){
    bus.$on('sanningFinished', (fileListJson) => {
      this.fileListJson = fileListJson;
      this.drawChart();
    })
  },

  methods: {
    /*
    ==========================================================
    Function : start scanning
    ==========================================================
     */
    drawChart: function(){
      this.$refs.generateChecksum.classList.remove('disabled');
      this.$refs.generateChecksum.removeAttribute("disabled");

      var ctxD = this.$refs.fileChecksumPercentage;
      var fielWithChecksumNum = this.fileListJson.data.filter(x => x.hasChecksum).length;
      var fileWithoutChecksumNum = this.fileListJson.data.length - fielWithChecksumNum;
      new Chart(ctxD, {
        type: 'doughnut',
        data: {
          labels: ["Files Without Checksum", "Files With Checksum"],
          datasets: [{
            data: [fileWithoutChecksumNum, fielWithChecksumNum],
            backgroundColor: ["red","green"],
          }]
        },
        options: {
          responsive: true
        }
      });
    },
    generateChecksum: function () {
      this.buttonFunctionType = this.buttonFunctionCancel;
      axios
        .get(generateUrl("apps/filechecksum/api/generatechecksum"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
        });
    },
  },
  components: {
  },
}
</script>

<style scoped lang="scss">
::v-deep {
  @import 'node_modules/bootstrap/scss/bootstrap';
  @import 'node_modules/bootstrap-vue/src/index.scss';
}
</style>
