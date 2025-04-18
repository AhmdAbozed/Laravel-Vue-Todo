export function getTimeFromDateString(date: string) {
    return { hour: (date.split(' ')[1].split(':')[0]), minute: (date.split(' ')[1].split(':')[1]) }
}

export function getDayFromDateString(date: string) {
    return date.split(' ')[0]
}
export async function signout() {
    try {
        await fetch('/logout', {
            method: 'POST',
            headers: {
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'include'
        });

        window.location.href = '/login';
    } catch (err) {
        console.error('Logout failed', err);
    }
};
