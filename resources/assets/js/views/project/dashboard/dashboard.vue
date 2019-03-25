<template>
  <!--  <v-chart :options="polar"/>-->
  <Row>
    <Col span="6">
      <Card>
        <div style="text-align:center;font-size:20px;line-height: 200px;height: 200px">
          <span><b>sdfsdf</b></span>
          <span style="font-size: 12px"><b>sdfsdf</b></span>
        </div>
      </Card>
    </Col>
    <Col span="6">
      <Card>
        <div style="text-align:center;font-size:20px;line-height: 200px;height: 200px">
          <span><b>sdfsdf</b></span>
          <span style="font-size: 12px"><b>sdfsdf</b></span>
        </div>
      </Card>
    </Col>
    <Col span="6">
      <Card>
        <div style="text-align:center;font-size:20px;line-height: 200px;height: 200px">
          <span><b>sdfsdf</b></span>
          <span style="font-size: 12px"><b>sdfsdf</b></span>
        </div>
      </Card>
    </Col>
    <Col span="6">
      <Card>
        <div style="text-align:center;font-size:20px;line-height: 200px;height: 200px">
          <span><b>sdfsdf</b></span>
          <span style="font-size: 12px"><b>sdfsdf</b></span>
        </div>
      </Card>
    </Col>
  </Row>
</template>

<style>
  .echarts {
    width: 100%;
    height: 100%;
  }
</style>

<script>
  import ECharts from 'vue-echarts'
  import 'echarts/lib/chart/line'
  import 'echarts/lib/component/polar'

  export default {
    components: {
      'v-chart': ECharts
    },
    data() {
      let data = []

      for (let i = 0; i <= 360; i++) {
        let t = i / 180 * Math.PI
        let r = Math.sin(2 * t) * Math.cos(2 * t)
        data.push([r, i])
      }

      return {
        polar: {
          title: {
            text: '极坐标双数值轴'
          },
          legend: {
            data: ['line']
          },
          polar: {
            center: ['50%', '54%']
          },
          tooltip: {
            trigger: 'axis',
            axisPointer: {
              type: 'cross'
            }
          },
          angleAxis: {
            type: 'value',
            startAngle: 0
          },
          radiusAxis: {
            min: 0
          },
          series: [
            {
              coordinateSystem: 'polar',
              name: 'line',
              type: 'line',
              showSymbol: false,
              data: data
            }
          ],
          animationDuration: 2000
        }
      }
    }
  }
</script>