<template>
  <el-card shadow="hover">
    <el-table :data="files" style="width: 100%" :header-cell-style="{'text-align':'center'}"
              :cell-style="{'text-align':'center'}">
      <el-table-column>
        <template slot="header">
          Movie
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="Movies"/>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          <el-button @click="openConfForm('MotionCor')">Motion-corr R</el-button>
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="MotionCor"/>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          <el-button @click="openConfForm('CTF')">CTF R</el-button>
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="CTF"/>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          Mark
        </template>
        <template slot-scope="scope">
          <el-tag
              :type="scope.row['Mark']==='good' ? 'success' : 'info'"
              disable-transitions>{{scope.row['Mark']}}
          </el-tag>
        </template>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          <el-button @click="openConfForm('Pick')">Pick</el-button>
        </template>
        <el-tag type="primary" disable-transitions>1000</el-tag>
      </el-table-column>
      <el-table-column>
        <template slot="header">
          Extract R
        </template>
        <imgInTableCell slot-scope="scope" :row="scope.row" module="Extract"/>
      </el-table-column>
    </el-table>
    <div class="tool-box">
      <el-button type="primary" size="small">TEST</el-button>
      <el-button type="danger" size="small">RUN ALL</el-button>
      <el-button type="success" size="small">SAVE</el-button>
    </div>
    <el-dialog title="参数设置" :visible.sync="dialogVisible" width="80%">
      <confView :forms="forms[step]" :module.sync="forms['_current'][step]"/>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="setConfig">确 定</el-button>
      </span>
    </el-dialog>
  </el-card>
</template>

<script>
  import projectAPI from '../api/project';
  import imgInTableCell from '../components/overview/imgInTableCell';
  import confView from "../components/overview/confView";

  export default {
    name: "Overview",
    components: {
      imgInTableCell,
      confView
    },
    data() {
      return {
        files: [],
        dialogVisible: false,
        forms:JSON.parse(JSON.stringify(this.$store.getters.getConfig)),
        step:''
      }
    },
    computed: {
      dir() {
        return this.$store.getters.getProject.directory;
      }
    },
    methods: {
      openConfForm(step) {
        this.step=step;
        this.dialogVisible=true;
      },
      setConfig(){
        this.dialogVisible = false;
        console.log(this.forms);
      }
    },
    mounted() {
      //更新files
      projectAPI.overview(this.dir).then(res => {
        this.files.splice(0, this.files.length);
        res.data.forEach((item, index, array) => {
          this.files.push(item)
        });
      });
    }
  }
</script>

<style scoped>
  .tool-box {
    float: right;
    margin: 10px;
  }
</style>
