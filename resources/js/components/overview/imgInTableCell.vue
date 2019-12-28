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
        src:'',
        name:''
      }
    },
    mounted() {
      if (this.row[this.module] !== undefined) {
        var path = this.row[this.module];
        projectAPI.getMrc(path).then(r => {
          this.src = r.data;
        });
        this.name = this.row.name;
      }
    }
  }
</script>