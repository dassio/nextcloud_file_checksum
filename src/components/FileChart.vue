<template>
  <div id="filechart">
    <!-- getting the file list -->
    <button v-on:click="startScanning">Getting Files Meta Data</button>
    <!-- <h3> scannedFilesNum : {{scannedFilesNum}} </h3> -->
    <MyAnimatedNumber :value="scannedFilesNum" :duration="8000" v-if="scannedFilesNum > 0" />
  
    <div id="fetching-file-spinner" v-if="showSpinner">
      <Spinner size="medium" />
    </div><br/>

    <!-- view files -->
    <div id="fileList" v-if="fileListVisibility">{{ fileListJson }}</div><br/>


  </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import Spinner from "vue-simple-spinner";
import MyAnimatedNumber from "./AnimatedNumber.vue";


export default {
  name: "FileChart",

  data: function () {
    return {
      fileListJson: null,
      fileListVisibility: false,
      showSpinner: false,
      scannedFilesNum: 0,
    };
  },

  methods: {
    // get file list
    startScanning: function () {
      this.showSpinner = true;
      this.fileListVisibility = false;
      axios
        .get(generateUrl("apps/filechecksum/api/statistic/startscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
          this.showSpinner = false;
        });
      setInterval(this.checkingScanningProgress, 10000);
    },
    //checking scanning progress
    checkingScanningProgress: function () {
      axios
        .get(generateUrl("apps/filechecksum/api/statistic/status"))
        .then((response) => {
          var progress_response = response.data[0];
          this.fileListJson = progress_response;
          this.fileListVisibility = true;
          this.showSpinner = false;
          if (progress_response.progress == "not_finished") {
            this.scannedFilesNum = progress_response.fileNum;
          } else if(progress_response.progress == "finished") {
            this.scannedFilesNum = progress_response.fileNum;
            clearInterval(this.checkingScanningProgress);
            this.getFileStatistic();
          } else {
            this.scannedFilesNum = progress_response.fileNum;
          }
        });
    },
    //get final result
    getFileStatistic: function () {
      axios
        .get(generateUrl("apps/filechecksum/api/statistic"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
          this.showSpinner = false;
        });
    },
  },
  components: {
    Spinner,
    MyAnimatedNumber
  },
};
</script>

<style>
</style>
