<template>
  <el-dialog title="登陆" :visible.sync="show" :width="width"
             :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
    <el-form :model="form">
      <el-form-item label="邮箱" label-width="80">
        <el-input v-model="form.username" autocomplete="off"/>
      </el-form-item>
      <el-form-item label="密码" label-width="80">
        <el-input v-model="form.password" autocomplete="off" show-password/>
      </el-form-item>
      <el-alert v-show="!validation.isValid" :title="validation.text" :closable="false" type="error" show-icon/>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="login">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
  export default {
    name: "Login",
    data() {
      return {
        show: false,
        width: "50%",
        form: {username: '', password: ''},
        validation:{
          isValid:true,
          text:''
        }
      }
    },
    methods: {
      login() {
        this.validation.isValid = this.form.username.length>0 && this.form.password.length>0;
        if(!this.validation.isValid){
          this.validation.text = '请填写信息';
        }else{
          this.$store.dispatch('login',this.form).then(res=>{
            this.validation.isValid=true;
            this.validation.text='';
          }).catch(res=>{
            this.validation.isValid=false;
            this.validation.text=res;
          });
        }
        return false;
      },
      resize(){
        if(document.body.clientWidth<768){
          this.width="90%";
        }else if(document.body.clientWidth<1200){
          this.width="50%";
        }else{
          this.width="30%";
        }
      }
    },
    mounted() {
      this.resize();
      window.addEventListener('resize', this.resize);
    },
    watch:{
      '$store.state.user':function(newV,oldV){
        this.show = newV.name==='';
      }
    }
  }
</script>
