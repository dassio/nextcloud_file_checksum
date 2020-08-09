<template>
  <animated-number
    :value="value"
    :formatValue="formatToPrice"
    :duration="duration"
  />
</template>
<script>
import AnimatedNumber from "animated-number-vue";

export default {
  components: {
    AnimatedNumber
  },
  props: ['value','duration'],
  methods: {
    formatToPrice(value) {
      return `<h3> ${Number(value).toFixed(0)}</h1>`;
    }
  },
  watch:{
    'value':function(){
      console.log(this.value)
    }
  }
};
</script>