import request from '../utils/request'

/**
 * 发行费分配概览 - 月报
 * @param startMonth 开始时间
 * @param endMonth 结束时间
 * @returns {*}
 */
export function getFxfOverviewMonthData(startMonth, endMonth) {
  return request({
    url: '/api/fxfoverviewmonth',
    method: 'post',
    data: {
      startMonth,
      endMonth
    }
  })
}
