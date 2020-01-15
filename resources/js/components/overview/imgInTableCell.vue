<template>
  <div>
    <el-image :src="src"
              style="width:100px;height:100px;" fit="fill"
              :preview-src-list="[src]">
      <div slot="placeholder" class="image-slot">
        加载中<span class="dot">...</span>
      </div>
      <div slot="error" class="image-slot">
        {{errorMsg}}
      </div>
    </el-image>
    <div v-if="module==='Movies'">{{name}}</div>
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
        errorMsg:'执行中...',
      }
    },
    mounted() {
      projectAPI.getPng(this.module,this.name,this.ext).then(res=>{
        if(res.data.data){this.src=res.data.data;}
      })
    }
  }
</script>