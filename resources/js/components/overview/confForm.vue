<template>
  <el-form class="conf_form" ref="form" label-width="80px">
    <el-form-item v-for="arg in form" v-if="arg.type!=='hidden'" :label="arg.title" :key="arg.name">
      <el-input v-if="arg.type==='string'" v-model="arg.value"/>
      <el-input-number v-if="arg.type==='int'" v-model="arg.value" :precision="0" :step="1"/>
      <el-input-number v-if="arg.type==='float'" v-model="arg.value" :precision="2" :step="0.1"/>
    </el-form-item>
  </el-form>
</template>

<script>
  export default {
    name:'confForm',
    props:{form:Array}
  }
</script>

<style scoped>
  .conf_form{
    margin:20px;
  }
</style>