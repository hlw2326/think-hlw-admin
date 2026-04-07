const autocannon = require('autocannon')

const urls = [
  'https://qz2-api.ka57.net/index/index/index3',
  'https://vue3.ka57.net/api/app/mini/test/apps',
]

const opts = {
  connections: 200,
  duration: 30,
  pipelining: 5,
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms))
}

async function runNext(index) {
  if (index >= urls.length) return
  const url = urls[index]
  await new Promise((resolve) => {
    const instance = autocannon({ ...opts, url }, (err, result) => {
      if (err) {
        console.error(err)
      } else {
        autocannon.printResult(result)
      }
      resolve()
    })
    autocannon.track(instance, { renderProgressBar: true })
  })
  if (index + 1 < urls.length) {
    console.log('休息 10 秒...')
    await sleep(10000)
  }
  await runNext(index + 1)
}

runNext(0)
