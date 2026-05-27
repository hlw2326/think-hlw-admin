const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

// 1. 获取命令行传入的 Commit 提交信息
const commitMessage = process.argv.slice(2).join(' ');
if (!commitMessage) {
    console.error('❌ 错误：请提供 Git 提交信息！');
    console.log('用法示例：node git-push.js "feat: add hlw-status-bar"');
    process.exit(1);
}

const pkgPath = path.resolve(__dirname, 'uni/package.json');
const lockPath = path.resolve(__dirname, 'uni/pnpm-lock.yaml');

// 2. 将当前物理磁盘上的内容读取并缓存到内存中
const currentPkg = fs.readFileSync(pkgPath, 'utf8');
const currentLock = fs.existsSync(lockPath) ? fs.readFileSync(lockPath, 'utf8') : null;

try {
    console.log('🧹 [1/4] 正在临时将 package.json 中的本地链接替换为干净的远程包版本...');
    
    // 解析 package.json 并把本地链接版本替换为标准的 npm 远程版本
    const pkgObj = JSON.parse(currentPkg);
    let changed = false;

    if (pkgObj.dependencies && pkgObj.dependencies['@hlw-uni/mp-vue']?.startsWith('link:')) {
        pkgObj.dependencies['@hlw-uni/mp-vue'] = '^2.1.71';
        changed = true;
    }
    if (pkgObj.devDependencies && pkgObj.devDependencies['@hlw-uni/mp-vite-plugin']?.startsWith('link:')) {
        pkgObj.devDependencies['@hlw-uni/mp-vite-plugin'] = '^1.0.51';
        changed = true;
    }

    if (changed) {
        fs.writeFileSync(pkgPath, JSON.stringify(pkgObj, null, 4), 'utf8');
        console.log('   ✅ 已临时将依赖版本替换为远程版本');
    }

    // 针对 pnpm-lock.yaml，我们临时通过 Git 检出远程干净的锁文件，防止本地 link 标识被提交
    if (currentLock) {
        console.log('🧹 [2/4] 正在临时从 Git 中检出干净的 pnpm-lock.yaml 锁文件...');
        execSync('git checkout HEAD -- uni/pnpm-lock.yaml', { stdio: 'inherit' });
    }

    // 3. 执行 Git 暂存与提交
    console.log('🚀 [3/4] 正在执行 git add 与 git commit...');
    execSync('git add .', { stdio: 'inherit' });
    execSync(`git commit -m "${commitMessage}"`, { stdio: 'inherit' });

    // 4. 执行 Git 推送
    console.log('☁️ [4/4] 正在将代码推送至云端 GitHub 仓库...');
    execSync('git push', { stdio: 'inherit' });

    console.log('🎉 恭喜！代码已成功推送至云端！');
} catch (error) {
    console.error('❌ 执行过程中遇到了错误：', error.message);
} finally {
    // 5. 无论如何（哪怕 commit/push 失败），都必须立刻将内存中原有的本地 link 配置写回磁盘，还原调试环境
    console.log('🔄 正在将您的 package.json & pnpm-lock.yaml 还原为本地联调状态...');
    fs.writeFileSync(pkgPath, currentPkg, 'utf8');
    if (currentLock) {
        fs.writeFileSync(lockPath, currentLock, 'utf8');
    }
    console.log('✨ 本地联调环境已完美恢复，您可以继续开发调试！');
}
