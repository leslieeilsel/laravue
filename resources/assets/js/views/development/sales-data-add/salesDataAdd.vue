<template>
  <Card>
    <div>
      <Form ref="form" :model="form" :label-width="160">
        <Row>
          <Col span="8">
            <FormItem label="用户号码" prop="user_mobile">
              <Input v-model="form.user_mobile" placeholder=""></Input>
            </FormItem>
          </Col>
          <Col span="8">
            <FormItem label="是否新用户" prop="is_new_user" >
              <Select v-model="form.is_new_user" filterable @on-change="changeUpNewUser">
                <Option v-for="item in dict.is_new_user" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="8">
            <FormItem label="产品类型" prop="project_type">
              <Select v-model="form.project_type" filterable>
                <Option v-for="item in dict.project_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="8">
            <FormItem label="终端类型" prop="terminal_type">
              <Select v-model="form.terminal_type" filterable>
                <Option v-for="item in dict.terminal_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="8">
            <FormItem label="业务类型" prop="business_type">
              <Select v-model="form.business_type" filterable>
                <Option v-for="item in dict.business_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="8">
            <FormItem label="积分" prop="int_num">
              <Input type="text" :rows="3" v-model="form.int_num" placeholder="请输入..."/>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="8">
            <FormItem label="区域" prop="area">
              <Input type="text" :rows="3" v-model="form.area" placeholder="请输入..."/>
            </FormItem>
          </Col>
          <Col span="8">
            <FormItem label="填报人" prop="applicant">
              <Input v-model="form.applicant" type="text" :rows="3" placeholder="请输入..."></Input>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="8">
            <FormItem label="日期" prop="date_time">
              <DatePicker type="date" placeholder="请选择"
                          v-model="form.date_time"></DatePicker>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="8">
            <FormItem label="套餐类型" prop="meal_type">
              <Select v-model="form.meal_type" filterable  @on-change="changeMealType">
                <Option v-for="item in dict.meal_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="8">
            <FormItem label="套餐" prop="meal">
              <Select v-model="form.meal" filterable>
                <Option v-for="item in dict.meal" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="2">&nbsp;</col>
            <Button @click="add_set_meal" type="primary" icon="md-add"></Button>
        </Row>
        <Row v-for="(v, index) in lists" :key="index">
          <Col span="8">
            <FormItem label="升级套餐类型" :prop="'up_meal_type_'+index">
              <Select v-model="v.up_meal_type" filterable  @on-change="changeUpMealType">
                <Option v-for="item in dict.up_meal_type" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="8">
            <FormItem label="升级套餐" :prop="'up_meal'+index">
              <Select v-model="v.up_meal" filterable>
                <Option v-for="item in v.option_v" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col span="1">&nbsp;</col>
          <Col span="3">
            <Button @click="del_set_meal(index)" type="primary" icon="md-remove"></Button>
          </col>
        </Row>
      </Form>
      <div slot="footer" class="footer_float">
        <Button @click="handleReset('form')" :loading="loading">重置</Button>
        <Button
          @click="submitF('form')"
          :loading="submitLoading"
          type="primary"
          style="margin-left:8px"
        >保存
        </Button>
      </div>
      <div class="clear"></div>
    </div>
  </Card>
</template>
<script>
  import {
    salesDataAdd,dictData
  } from '../../../api/value';
  import './salesDataAdd.css'

  export default {
    data() {
      return {
        data: [],
        tableLoading: true,
        loading: false,
        submitLoading: false,
        form: {
          user_mobile: '',
          is_new_user: '',
          business_type: '',
          int_num: '',
          area: '',
          applicant: '',
          project_type: '',
          meal: '',
          date_time: '',
          meal_info:''
        },
        dictName: {
          project_type_v: '产品类型-价值',
          project_type_d: '产品类型-发展',
          business_type: '业务类型',
          terminal_type:'终端类型',
          meal_type: '套餐',
          meal_0: '融合套餐',
          meal_1: '单卡套餐',
          meal_2: '智慧企业套餐',
          up_meal_type: '升级套餐',
          up_meal_0: '智慧家庭升级包',
          up_meal_1: '5G升级包',
          up_meal_2: '加第二路宽带',
          up_meal_3: '加卡',
          is_new_user: '是否新用户'
        },
        dict: {
          project_type_v:[],
          project_type_d:[],
          project_type: [],
          business_type: [],
          terminal_type:[],
          meal_type: [],
          meal: [],
          meal_0: [],
          meal_1: [],
          meal_2: [],
          up_meal_type: [],
          up_meal:[],
          up_meal_0: [],
          up_meal_1: [],
          up_meal_2: [],
          up_meal_3: [],
          is_new_user: []
          
        },
        lists:[]
      }
    },
    methods: {
      init() {
        this.getDictData();
      },
      handleReset(name) {
        this.$refs[name].resetFields();
        this.lists=[];
      },
      submitF(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.submitLoading = true;
            let up_meal=[];
            this.lists.forEach(function (e) {
              up_meal.push({meal:e.up_meal,meal_type:e.up_meal_type});
            });
            this.form.meal_info={
                                  meal:{meal:this.form.meal,meal_type:this.form.meal_type},
                                  up_meal:up_meal
                                }            
            salesDataAdd(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("销售数据填报成功");
                this.modal = false;
                this.cancel();
                this.lists=[];
              } else {
                this.$Message.error('销售数据填报失败!');
              }
            });
          }
        })
      },
      cancel() {
        this.$refs.form.resetFields();
      },
      getDictData() {
        dictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;            
          }
        });
      },
      changeMealType(){
        if(this.form.meal_type===0){
          this.dict.meal=this.dict.meal_0;
        }else if(this.form.meal_type===1){
          this.dict.meal=this.dict.meal_1;
        }else if(this.form.meal_type===2){
          this.dict.meal=this.dict.meal_2;
        }
      },
      changeUpMealType(){
        let _this=this;
        this.lists.forEach(function (e) {
          if(e.up_meal_type===0){
            e.option_v=_this.dict.up_meal_0;
          }else if(e.up_meal_type===1){
            e.option_v=_this.dict.up_meal_1;
          }else if(e.up_meal_type===2){
            e.option_v=_this.dict.up_meal_2;
          }else if(e.up_meal_type===3){
            e.option_v=_this.dict.up_meal_3;
          }
        });
      },
      add_set_meal(){
        let len=0;
        if(this.lists.length>0){
          len=parseInt(this.lists.length)+1
        }        
        this.lists.push({'up_meal_type':'','up_meal':'','option_v':[]});
      },
      del_set_meal(index){
        this.lists.splice(index,1);
      },
      changeUpNewUser(){
        if(this.form.is_new_user===0){
          this.dict.project_type=this.dict.project_type_v;
        }else if(this.form.is_new_user===1){
          this.dict.project_type=this.dict.project_type_d;
        }
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
<style scoped>
.clear{
  clear:both;
}
.footer_float{
  float: right;
}
</style>
