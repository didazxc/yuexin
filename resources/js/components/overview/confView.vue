<template>
  <el-tabs :value="module" @input="handleInput">
    <el-tab-pane v-for="(form,name) in forms" :key="name" :label="name" :name="name">
      <confForm :form="form.args"/>
    </el-tab-pane>
  </el-tabs>
</template>

<script>
  import confForm from "./confForm";
  export default {
    name: "confView",
    props:{forms:Object,module:String},
    components:{confForm},
    methods: {
      handleInput(e){
        this.$emit('update:module',e);
      }
    }
  }
</script>
