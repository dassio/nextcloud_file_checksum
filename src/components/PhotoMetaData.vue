<template>
  <div id="photoMetaData">
    <b-button disabled variant="primary" id="photoMeta">{{photoMeta}}</b-button>
    <canvas id="photoMetaCanvas"></canvas>
  </div>
</template>

<script>
export default {
  name: "PhotoMetaData",
  data: function(){
    return {
      photoMeta: "Photo Meta Info"
    }
  },
}
</script>
