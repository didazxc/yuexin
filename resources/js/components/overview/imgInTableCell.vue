<template>
  <div>
    <mrcImg :path="path" styleStr="width:100px;height:100px;"/>
    <div>{{name}}</div>
  </div>
</template>

<script>
  import mrcImg from "../common/mrcImg";

  export default {
    name: "imgInTableCell",
    components:{mrcImg},
    props:{row:{type:Object},module:{type:String}},
    data(){
      return {
        path:null,
        name:''
      }
    },
    mounted() {
      if (this.row[this.module] !== undefined) {
        this.path = this.row[this.module];
        this.name = this.row.name;
      }
    }
  }
</script>