<template>
  <div>
    <el-image :src="src"
              style="width: 100px; height: 100px" fit="fill"
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
    props:{row:{type:Object},module:{type:String}},
    data(){
      return {
        src:"undefined",
        name:''
      }
    },
    mounted() {
      if (this.row[this.module] !== undefined) {
        var f = this.row[this.module][0];
        var path = f.path;
        if (this.module === "Gctf") {
          path = path.substring(0, path.lastIndexOf('.')) + ".ctf";
        }
        projectAPI.getMrc(path).then(r => {
          this.src = r.data;
          //this.row[this.module][0].src = this.src;
        });
        this.name = f.name;
      }
    }
  }
</script>