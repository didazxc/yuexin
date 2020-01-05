<template>
  <el-image :src="src"
            :style="styleStr" fit="fill"
            :preview-src-list="[src]">
    <div slot="placeholder" class="image-slot">
      加载中<span class="dot">...</span>
    </div>
  </el-image>
</template>

<script>
  import projectAPI from "../../api/project";
  export default {
    name: "mrcImg",
    props:{path:{type:String},styleStr:{type:String,default:''}},
    data(){
      return {
        src:'',
      }
    },
    watch:{
      path(newVal,oldVal){
        projectAPI.getMrc(newVal).then(r => {
          this.src = r.data;
        });
      }
    },
    mounted() {
      if(this.path!==null){
        projectAPI.getMrc(this.path).then(r => {
          this.src = r.data;
        });
      }
    }
  }
</script>