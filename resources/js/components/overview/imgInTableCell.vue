<template>
  <div>
    <el-image :src="src" :title="name"
              style="width:100px;height:100px;" fit="fill"
              :preview-src-list="[src]">
      <div slot="placeholder" class="image-slot">
        加载中<span class="dot">...</span>
      </div>
      <div slot="error" class="image-slot">
        {{status}}
      </div>
    </el-image>
  </div>
</template>

<script>
  import projectAPI from "../../api/project";

  export default {
    name: "imgInTableCell",
    props:{name:{type:String},module:{type:String},status:{type:String},ext:{type:String,default:'mrc'}},
    data(){
      return {
        src:''
      }
    },
    methods:{
      updateSrc(){
        if(this.status==='执行完毕' || this.module==='Movies') {
          projectAPI.getPng(this.module, this.name, this.ext).then(res => {
            if (res.data.data) {
              this.src = res.data.data;
            } else {
              this.src = '';
            }
          })
        }else{
          this.src = '';
        }
      }
    },
    watch:{
      status(val){
        this.updateSrc()
      }
    },
    mounted() {
      this.updateSrc()
    }
  }
</script>