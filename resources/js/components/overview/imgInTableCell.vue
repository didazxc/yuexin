<template>
  <div>
    <el-image :src="src"
              style="width:100px;height:100px;" fit="fill"
              :preview-src-list="[src]">
      <div slot="placeholder" class="image-slot">
        加载中<span class="dot">...</span>
      </div>
    </el-image>
    <div>{{name}}</div>
  </div>
</template>

<script>
  import projectAPI from "../../api/project";

  export default {
    name: "imgInTableCell",
    props:{name:{type:String},module:{type:String},ext:{type:String,default:'mrc'}},
    data(){
      return {
        src:'',
      }
    },
    mounted() {
      projectAPI.getPng(this.module,this.name,this.ext).then(res=>{
        this.src=res.data;
      })
    }
  }
</script>