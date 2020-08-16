<template>
  <div id="scan">
    <!-- getting the file list -->
    <div id="scanning">
      <button v-on:click="ScanningFunction" >{{ buttonFunctionType }}</button>

      <MyAnimatedNumber :value="scannedFilesNum" :duration="statusCheckInterval" v-if="scannedFilesNum > 0" />

      <hr v-if="buttonFunction=='cancel'" >
    </div>

    <!-- view files -->
    <div id="fileList" v-if="fileListVisibility">{{ fileListJson }}</div><br/>


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
      fileListVisibility: false,
      scannedFilesNum: 0,
      intervalId:null,
      buttonFunctionStart: "Start Scanning",
      buttonFunctionCancel: "Cancel Scanning",
      buttonFunctionRescan: "Rescanning Files",
      buttonFunctionType: "Start Scanning",
      statusCheckInterval:10000,
    };
  },

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
        .get(generateUrl("apps/filechecksum/api/statistic/startscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
        });
      this.intervalId = setInterval(this.checkingScanningProgress, this.statusCheckInterval);
    },
    cancelScanning: function() {
      this.buttonFunctionType = this.buttonFunctionStart;
      axios
        .get(generateUrl("apps/filechecksum/api/statistic/cancelscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = false;
        });
      this.scannedFilesNum = 0;
      this.buttonFunction = "start";
    },
    restartScanning: function(){
      this.scannedFilesNum = 0;
      this.buttonFunctionType = this.buttonFunctionCancel;
      axios
        .get(generateUrl("apps/filechecksum/api/statistic/restartscanning"))
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
        .get(generateUrl("apps/filechecksum/api/statistic/status"))
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
        .get(generateUrl("apps/filechecksum/api/statistic"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
        });
        bus.$emit("sanningFinished",this.fileListJson);
    },
  },

  components: {
    MyAnimatedNumber
  },
};
</script>

<style lang="scss">

$scanner_shadow: #666;
$scanner_light: red;
$scanner_background: #333;

#scanning hr {
    position: relative;
    margin: 10px 10px 10px 4px;
    font-size: 0;
    height: 4px;
    border: 0;
    width:20%;
    border-radius: 25px;
  
    &:after {
      content: '';
      position: absolute;
      top: 0;
      width: 100%;
      height: 4px;
      background-color: $scanner_background;
      background-image: linear-gradient(90deg, $scanner_background, $scanner_light), linear-gradient(90deg, $scanner_light, $scanner_background);
      background-size: 15% 100%;
      background-position: -30% 0, 130% 0;
      background-repeat: no-repeat;
      /*box-shadow: 1px 1px 1px $scanner_shadow;*/
      animation: scan 3s infinite;
    }
  .scan {
    
  }
}

@keyframes scan {
    0% {background-position:-20% 0, 120% 0;}
    50% {background-position:120% 0, 120% 0;}
    100% {background-position:120% 0, -20% 0;}
}
</style>
