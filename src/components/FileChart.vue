<template>
  <div id="filechart">
    <button v-on:click="getFileMetaData($refs.fileChartCanvas)">Getting Files Meta Data</button>
    <br />
    <div id="fileList" v-if="fileListVisibility">{{ fileListJson }}</div>
    <div id="fetching-file-spinner" v-if="showSpinner">
      <Spinner size="medium" />
    </div>
    <!-- draw file statistic -->
    <canvas ref="fileChartCanvas" width="400" height="400"></canvas>
  </div>
</template>

<script>
import axios from "@nextcloud/axios";
import { generateUrl } from "@nextcloud/router";
import Spinner from "vue-simple-spinner";
import Chart from "chart.js";

export default {
  name: "FileChart",

  data: function () {
    return {
      fileListJson: null,
      fileListVisibility: false,
      showSpinner: false,
    };
  },

  methods: {
    // get file list
    getFileMetaData: function (fileChartCanvas) {
      this.showSpinner = true;
      this.fileListVisibility = false;
      axios
        .get(generateUrl("apps/filechecksum/api/statistic"))
        .then((response) => {
          this.fileListJson = response;
          this.fileListVisibility = true;
          this.showSpinner = false;
          this.drawFileChart(fileChartCanvas);
        });
    },
    // File Chart show file meta data percentage
    drawFileChart: function (fileChartCanvas) {
      new Chart(fileChartCanvas, {
        type: "bar",
        data: {
          labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
          datasets: [
            {
              label: "# of Votes",
              data: [12, 19, 3, 5, 2, 3],
              backgroundColor: [
                "rgba(255, 99, 132, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(255, 206, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(255, 159, 64, 0.2)",
              ],
              borderColor: [
                "rgba(255, 99, 132, 1)",
                "rgba(54, 162, 235, 1)",
                "rgba(255, 206, 86, 1)",
                "rgba(75, 192, 192, 1)",
                "rgba(153, 102, 255, 1)",
                "rgba(255, 159, 64, 1)",
              ],
              borderWidth: 1,
            },
          ],
        },
        options: {
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                },
              },
            ],
          },
        },
      });
    },
  },
  components: {
    Spinner,
  },
};
</script>

<style>
</style>
