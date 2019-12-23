<template>
  <div>
    <el-image :src="row[module]===undefined?'':row[module][0]['src']"
              style="width: 100px; height: 100px" fit="fill"
              :preview-src-list="[row[module][0]['src']]">
      <div slot="placeholder" class="image-slot">
        加载中<span class="dot">...</span>
      </div>
    </el-image>
    <div>{{row[module][0]['name']}}</div>
  </div>
</template>

<script>
  import projectAPI from "../../api/project";

  export default {
    name: "imgInTableCell",
    props:{row:{type:Object},module:{type:String}},
    mounted(){
      var path = this.row[this.module][0].path;
      if(this.module==="/Gctf"){
        path=path.substring(0,path.lastIndexOf('.'))+".ctf";
      }
      projectAPI.getMrc(path).then(r=>{
        this.row[this.module][0].src=r.data;
      });
    }
  }
</script>