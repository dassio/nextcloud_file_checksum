<template>
  <div id="filechart">
    <!-- getting the file list -->
    <div id="scanning">
      <button v-on:click="startScanning">{{ buttonFunction }}</button>
      <MyAnimatedNumber :value="scannedFilesNum" :duration="10000" v-if="scannedFilesNum > 0" />
      <hr v-if="showScanning" >
    </div>

    <!-- view files -->
    <div id="fileList" v-if="fileListVisibility">{{ fileListJson }}</div><br/>


  </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import MyAnimatedNumber from "./AnimatedNumber.vue";


export default {
  name: "FileChart",

  data: function () {
    return {
      fileListJson: null,
      fileListVisibility: false,
      scannedFilesNum: 0,
      intervalId:null,
      buttonFunction: "Start Scanning Files",
      showScanning: false,
    };
  },

  methods: {
    /*
    ==========================================================
    Function : start scanning 
    ==========================================================
    */
    startScanning: function () {
      this.fileListVisibility = false;
      axios
        .get(generateUrl("apps/filechecksum/api/statistic/startscanning"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
        });
      this.buttonFunction = "Cancle Scanning";
      this.showScanning = true;
      this.intervalId = setInterval(this.checkingScanningProgress, 10000);
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
            this.showScanning = false;
            this.buttonFunction = "Rescan";
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
