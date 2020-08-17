<template>
  <div id="scan">
    <!-- getting the file list -->
    <div id="scanning">
      <b-button variant="primary" v-on:click="ScanningFunction" >{{ buttonFunctionType }}</b-button>
      <b-button variant="info" disabled v-if="buttonFunctionType==buttonFunctionCancel" >
        <div class="spinner-border spinner-border-sm" role="status" >
          <span class="sr-only">Scanning Files....</span>
        </div>
        <MyAnimatedNumber :value="scannedFilesNum" :duration="statusCheckInterval" v-if="scannedFilesNum > 0" />
      </b-button>
    </div>
  </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import MyAnimatedNumber from "./AnimatedNumber.vue";
import { bus } from '../main'


export default {
  name: "Scanning",

  data: function () {
    return {
      fileListJson: null,
      scannedFilesNum: 0,
      intervalId:null,
      buttonFunctionStart: "Start Scanning",
      buttonFunctionCancel: "Cancel Scanning",
      buttonFunctionRescan: "Rescanning Files",
      buttonFunctionType: "Start Scanning",
      statusCheckInterval:10000,
    };
  },

//  mounted: function(){
//    axios
//      .get(generateUrl("apps/files_checksum/api/statistic/status"))
//      .then((response) => {
//        var progress_response = response.data[0];
//        this.fileListJson = progress_response;
//        if(progress_response.progress == "finished") {
//          //update ui
//          this.scannedFilesNum = progress_response.fileNum;
//          this.buttonFunctionType = this.buttonFunctionRescan;
//
//          // get final reuslt
//          this.getFileStatistic();
//        }
//      });
//
//  },

  methods: {
    /*
    ==========================================================
    Function : start scanning
    ==========================================================
     */
    ScanningFunction : function(){
      switch(this.buttonFunctionType){
        case this.buttonFunctionStart:
          this.startScanning();
          break
        case this.buttonFunctionCancel:
          this.cancelScanning();
          break
        case this.buttonFunctionRescan:
          this.restartScanning();
      }
    },
    startScanning: function () {
      this.buttonFunctionType = this.buttonFunctionCancel;
      axios
        .get(generateUrl("apps/files_checksum/api/statistic/startscanning"))
        .then((response) => {
          this.fileListJson = response;
        });
      this.intervalId = setInterval(this.checkingScanningProgress, this.statusCheckInterval);
    },
    cancelScanning: function() {
      this.buttonFunctionType = this.buttonFunctionStart;
      axios
        .get(generateUrl("apps/files_checksum/api/statistic/cancelscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = false;
        });
      this.scannedFilesNum = 0;
      this.buttonFunction = "start";
      clearInterval(this.intervalId);
    },
    restartScanning: function(){
      this.scannedFilesNum = 0;
      this.buttonFunctionType = this.buttonFunctionCancel;
      axios
        .get(generateUrl("apps/files_checksum/api/statistic/restartscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
        });
      this.intervalId = setInterval(this.checkingScanningProgress, this.statusCheckInterval);
    },
    /*
    ==========================================================
    Function : checking scanning progress
    ==========================================================
     */
    checkingScanningProgress: function () {
      axios
        .get(generateUrl("apps/files_checksum/api/statistic/status"))
        .then((response) => {
          var progress_response = response.data[0];
          this.fileListJson = progress_response;
          this.fileListVisibility = true;
          if (progress_response.progress == "not_finished") {
            this.scannedFilesNum = progress_response.fileNum;
          } else if(progress_response.progress == "finished") {
            //update ui
            this.scannedFilesNum = progress_response.fileNum;
            setTimeout( () =>  this.buttonFunctionType = this.buttonFunctionRescan, this.statusCheckInterval+2000);

            // get final reuslt
            this.getFileStatistic();
            clearInterval(this.intervalId);
          } else {
            this.scannedFilesNum = progress_response.fileNum;
          }
        });
    },
    /*
    ==========================================================
    Function : get final result
    ==========================================================
     */
    getFileStatistic: function () {
      axios
        .get(generateUrl("apps/files_checksum/api/statistic"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
          bus.$emit("sanningFinished",this.fileListJson);
        });
    },
  },

  components: {
    MyAnimatedNumber
  },
};
</script>

<style lang="scss" scoped>
::v-deep {
  @import 'node_modules/bootstrap/scss/bootstrap';
  @import 'node_modules/bootstrap-vue/src/index.scss';
}
</style>
